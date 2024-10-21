<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Imports\ProductSerialImport;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductSerialService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = ProductSerial::query();

        //condition data
        $this->applyActive($query, request: $request);

        // Select specific columns
        $query->select(['*']);

        // Sorting
        $this->applySorting($query, $request);

        // Searching
        $searchKeys = ['serial_number']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function unsoldProductSerials(Request $request, $id): Collection
    {
        return ProductSerial::where('product_id', $id)->where('sold', 0)->get();
    }

    public function store(Request $request)
    {
        $data = $this->prepareProductSerialData($request);

        if ($request->product_id) {
            $product = Product::with('inventory')->find($request->product_id);

            // Check if the product exists and has serials
            if (! $product) {
                throw new \Exception('Product not found');
            }

            if ($product->has_serials) {
                $serialsCount = ProductSerial::where('product_id', $product->id)->count();

                // Check inventory quantity against serials count
                if ($product->inventory->qty <= $serialsCount) {
                    throw new \Exception('Insufficient inventory quantity');
                }

                // Create the new serial number
                return ProductSerial::create($data);
            } else {
                throw new \Exception('Product does not have serials');
            }
        }
    }

    private function prepareProductSerialData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new ProductSerial())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        //$data['thumbnail'] = $this->ftpFileUpload($request, 'thumbnail', 'productSerial');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'productSerial');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): ProductSerial
    {
        return ProductSerial::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $productSerial = ProductSerial::findOrFail($id);
        $updateData = $this->prepareProductSerialData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return ! is_null($value);
        });
        $productSerial->update($updateData);

        return $productSerial;
    }

    public function destroy(int $id): bool
    {
        $productSerial = ProductSerial::findOrFail($id);
        $productSerial->serial_number .= '_'.Str::random(8);
        $productSerial->deleted_at = now();

        return $productSerial->save();
    }

    public function importProductSerials(Request $request)
    {

        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id', // Ensure product_id exists in the products table
            'file' => 'required|mimes:xlsx,xls,csv', // Validate file type
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $product = Product::find($request->product_id);

        // Check if the product has serials
        if ($product->has_serials == true) {

            // Import the file using ProductSerialImport and pass the product_id
            Excel::import(new ProductSerialImport($request->product_id), $request->file('file'));

            return $this->successResponse([], 'Serial numbers imported successfully', 200);
        } else {
            throw new \Exception('An error occurred while importing serial numbers');
        }
    }
}
