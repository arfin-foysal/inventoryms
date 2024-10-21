<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Sale;
use App\Models\SaleDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Sale::query();
        $query->with('client:id,name');

        //condition data
        $this->applyActive($query, $request);

        // Select specific columns
        $query->select(['*']);

        $filters = ['payment_status' => '=']; // add your filter columns
        $this->applyFilters($query, $request, $filters);

        // Sorting
        $this->applySorting($query, $request);


        // Searching
        $searchKeys = ['invoice_no']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }


    private function prepareSaleData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Sale())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        //payment status
        if ($request->due_amount > 0) {
            $data['payment_status'] = 'Due';
        } else {
            $data['payment_status'] = 'Paid';
        }


        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['invoice_no'] = $this->generateInvoiceNo();
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Sale
    {
        // Load sale with related models
        $sale = Sale::with(['client', 'saleDetails.product:id,name'])->findOrFail($id);

        $sale->saleDetails->each(function ($saleDetail) {
            $saleDetail->serials = $saleDetail->serials ?? [];
        });

        return $sale;
    }


    public function update(Request $request, int $id)
    {
        $sale = Sale::findOrFail($id);
        $updateData = $this->prepareSaleData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return ! is_null($value);
        });
        $sale->update($updateData);

        return $sale;
    }


    public function destroy(int $id): bool
    {
        $sale = Sale::findOrFail($id);
        $sale->name .= '_' . Str::random(8);
        $sale->deleted_at = now();

        return $sale->save();
    }

    public function generateInvoiceNo()
    {
        // Use a transaction to avoid race conditions and ensure uniqueness
        return DB::transaction(function () {
    
            // Get the current date in 'BBYYYYMMDD' format
            $datePrefix = 'BB' . date('Ymd');
    
            // Fetch the last sale record with today's date prefix
            $lastSale = Sale::where('invoice_no', 'like', $datePrefix . '%')->latest()->first();
    
            // Extract the last invoice number's suffix (the last 4 digits) if it exists, otherwise default to 0
            $lastInvoiceNo = $lastSale ? (int) substr($lastSale->invoice_no, -4) : 0;
    
            // Increment to get the next invoice number
            $nextInvoiceNo = $lastInvoiceNo + 1;
    
            // Generate the new invoice number
            $newInvoiceNo = $datePrefix . str_pad($nextInvoiceNo, 4, '0', STR_PAD_LEFT);
    
            // Ensure the invoice number is unique by checking for existing records with this number
            while (Sale::where('invoice_no', $newInvoiceNo)->exists()) {
                $nextInvoiceNo++;
                $newInvoiceNo = $datePrefix . str_pad($nextInvoiceNo, 4, '0', STR_PAD_LEFT);
            }
    
            // Return the unique invoice number
            return $newInvoiceNo;
        });
    }
    


    public function store(Request $request)
    {
        // Start the transaction
        DB::beginTransaction();

        try {
            // Prepare the sale data
            $data = $this->prepareSaleData($request);

            // Create the sale record
            $sale = Sale::create($data);

            // Handle product details if provided
            if ($request->filled('product_details')) {
                $productDetails = $this->prepareProductDetails($request->product_details);

                // Insert sale details
                $sale->saleDetails()->createMany($productDetails);

                // Update inventory and handle serial products
                $this->updateInventoryAndHandleSerials($productDetails);
            }

            // Insert sale account and account details
            $this->createSaleAccountAndDetails($sale, $request);

            // Commit the transaction if all is successful
            DB::commit();

            // Return the created sale record
            return $sale;
        } catch (Exception $e) {
            // Rollback the transaction on failure
            DB::rollBack();

            // Optionally rethrow the exception or handle it as needed
            throw $e;
        }
    }

    private function prepareProductDetails(array $productDetails)
    {
        // Flatten the serials from all products into one array
        $serials = collect($productDetails)->pluck('serials')->flatten()->toArray();

        // Process each product
        return array_map(function ($product) {
            // Calculate total price
            $product['total_price'] = $product['qty'] * $product['unit_price'];

            // Filter and concatenate valid serial numbers if they exist
            $product['serial_number'] = isset($product['serials']) && $product['serials'] ? implode(',', array_filter($product['serials'])) : null;

            return $product;
        }, $productDetails);
    }

    private function updateInventoryAndHandleSerials(array $productDetails)
    {
        foreach ($productDetails as $productDetail) {

            $isSerialProduct = Product::where('id', $productDetail['product_id'])->where('has_serials', 1)->exists();
            
            if($isSerialProduct== true && empty($productDetail['serials'])){ 
                throw new Exception("Serials are required for product ID {$productDetail['product_id']}", 422);
            }

            if($productDetail['qty'] !=count($productDetail['serials'])){
                throw new Exception("Quantity and Serials must be the same for product ID {$productDetail['product_id']}", 422);
            }

            
            // Update inventory
            $this->updateInventory($productDetail);

            // Handle serial products if applicable
            if (! empty($productDetail['serials'])) {
                $this->markSoldSerialProducts($productDetail);
            }
        }
    }

    private function updateInventory(array $productDetail)
    {
        $inventory = Inventory::where('product_id', $productDetail['product_id'])->first();
        if ($inventory) {
            $newQty = $inventory->qty - $productDetail['qty'];

            if ($newQty >= 0) {
                $inventory->decrement('qty', $productDetail['qty']);
                // $inventory->transaction_qty = true;
                $inventory->save();
            } else {
                throw new Exception("Insufficient inventory for product ID {$productDetail['product_id']}", 422);
            }
        }
    }

    private function markSoldSerialProducts(array $productDetail)
    {
        // Ensure that the number of serials matches the quantity
        if (count($productDetail['serials']) != $productDetail['qty']) {
            throw new Exception("Quantity and Serials must be the same for product ID {$productDetail['product_id']}", 422);
        }

        // Update the serials to mark them as sold
        ProductSerial::whereIn('id', $productDetail['serials'])->update(['sold' => 1]);
    }

    private function createSaleAccountAndDetails(Sale $sale, Request $request)
    {
        // Create sale account details
        $sale->saleAccountDetails()->create([
            'invoice_no' => $sale->invoice_no,
            'paid_amount' => $sale->paid_amount,
            'client_id' => $sale->client_id,
            'payment_method' => $request->payment_method,
            'description' => $request->description,
            'attachment' => $request->attachment,
            'transition_id' => $request->transition_id,
        ]);
    }


    public function productBySale(Request $request, int $id)
    {
        return SaleDetail::where('sale_id', $id)->select('id', 'sale_id', 'product_id', 'serial_number')->with('product:id,name')
            ->get();
    }
}
