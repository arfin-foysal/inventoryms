<?php

namespace App\Services;

use App\Exceptions\ErrorMessageException;
use App\Http\Traits\HelperTrait;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\SaleDetail;
use App\Models\StockEntry;
use App\Models\StockEntryDetail;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockEntryService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = StockEntry::query();

        $query->with(relations: 'vendor:id,name,company_name');

        //condition data
        $this->applyActive($query, $request);

        // Select specific columns
        $query->select(['*']);

        // Sorting
        $this->applySorting($query, $request);

        // Searching
        $searchKeys = ['invoice_number', 'received_date', 'price']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $this->prepareStockEntryData($request);
            $stockEntry = StockEntry::create($data);

            if ($request->filled('product_details')) {
                $stockEntry->StockEntryDetail()->createMany($request->product_details);
                // Pass the newly created stockEntry ID to handleInventory
                $this->handleInventory($request->product_details, $stockEntry->id);
            }

            DB::commit();
            return $stockEntry;
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('StockEntry creation failed', ['error' => $e->getMessage()]);

            // Return error response with message
            throw new ErrorMessageException($e->getMessage());
        }
    }

    private function prepareStockEntryData(Request $request, bool $isNew = true): array
    {
        $fillable = (new StockEntry())->getFillable();
        $data = $request->only($fillable);
        $data['image'] = $this->ftpFileUpload($request, 'image', 'image');

        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }
    
    private function saleCheck(StockEntry $stockEntry)
    {

        $productIds = $stockEntry->StockEntryDetail()
            ->pluck('product_id')
            ->toArray();

        // Check if any SaleDetail exists after stock entry creation
        $hasSales = SaleDetail::whereIn('product_id', $productIds)
            ->where('created_at', '>=', $stockEntry->created_at)
            ->exists();

        if ($hasSales) {
            throw new \Exception("Sale details exist. Cannot update stock entry.", 422);
        }
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $stockEntry = StockEntry::findOrFail($id);


            // Check if sales exist for this stock entry
            $this->saleCheck($stockEntry);

            $updateData = array_filter($this->prepareStockEntryData($request, false), fn($value) => !is_null($value));
            $stockEntry->update($updateData);

            if ($request->has('product_details')) {
                $this->syncProductDetails($stockEntry, $request->product_details);
                // Pass the stockEntry ID to handleInventory
                $this->handleInventory($request->product_details, $stockEntry->id);
            }

          
            DB::commit();
            return $stockEntry;
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('StockEntry update failed', ['error' => $e->getMessage()]);

            // Return error response with message
            throw new ErrorMessageException($e->getMessage());
        }
    }

    private function syncProductDetails(StockEntry $stockEntry, array $productDetails)
    {
        $oldDetails = $stockEntry->StockEntryDetail;
        foreach ($productDetails as $productDetail) {
            $oldDetail = $oldDetails->firstWhere('product_id', $productDetail['product_id']);
            if ($oldDetail) {
                $inv = Inventory::where('product_id', $productDetail['product_id'])->first();
                if ($inv) {
                    $inv->decrement('qty', $oldDetail->qty);
                }
            }
        }

        $stockEntry->StockEntryDetail()->delete();
        $stockEntry->StockEntryDetail()->createMany($productDetails);
    }

    protected function handleInventory(array $productDetails, $id = null)
    {
        foreach ($productDetails as $productDetail) {
            $product = Product::find($productDetail['product_id']);
            $hasSerials = $product->has_serials;

            // Ensure $id is passed when calling markSerialProducts
            if ($hasSerials && (!empty($productDetail['serials']) && $productDetail['qty'] == count($productDetail['serials']))) {
                $this->markSerialProducts($productDetail, $id);
            } elseif ($hasSerials) {
                throw new \Exception("Quantity and Serials must be the same for product ID {$productDetail['product_id']}", 422);
            }

            $inventory = Inventory::updateOrCreate(
                ['product_id' => $productDetail['product_id']],
                ['qty' => DB::raw("qty + {$productDetail['qty']}")]
            );
        }
    }

    public function stockProducts(int $id)
    {
        return StockEntryDetail::with(['product:id,name,image,sku,has_serials', 'product.serials'])
            ->where('stock_entry_id', $id)
            ->get()
            ->map(function ($detail) {
                $detail->setAttribute('has_fullfiled_serial', $detail->qty == $detail->product->serials->count());
                unset($detail->product->serials);
                return $detail;
            });
    }

    public function markSerialProducts(array $productDetail, int $id)
    {
        // Delete old serials based on product ID and stock entry ID
        ProductSerial::where('product_id', $productDetail['product_id'])
            ->where('stock_entry_id', $id)
            ->delete();

        // Prepare serial data for insertion
        $productSerials = array_map(function ($serial) use ($productDetail, $id) {
            return [
                'product_id' => $productDetail['product_id'],
                'stock_entry_id' => $id,  // Correctly pass the stock entry ID
                'serial_number' => $serial,
                'warranty_period_value' => $productDetail['warranty_period_value'] ?? null,
                'created_at' => now(),
                'created_by' => auth()->id(),
                'organization_id' => auth()->user()->organization_id
            ];
        }, $productDetail['serials']);

        // Insert the new serial data
        ProductSerial::insert($productSerials);
    }


    public function show(int $id): StockEntry
    {
        return StockEntry::with(['vendor:id,name', 'StockEntryDetail', 'StockEntryDetail.product:id,name,has_serials', 'StockEntryDetail.product.serials:id,product_id,serial_number,is_active,sold'])->findOrFail($id);
    }
    public function destroy(int $id): bool
    {
        // Check if the stock entry has any details
        $StockEntryDetail = StockEntryDetail::where('stock_entry_id', $id)->exists();
        if ($StockEntryDetail) {
            throw new Exception("Stock entry details exist. Cannot delete.", 422);
        }

        $stockEntry = StockEntry::findOrFail($id);
        $stockEntry->deleted_at = now();

        return $stockEntry->save();
    }
}
