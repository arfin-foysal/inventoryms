<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Product::query();
        $query->with(['inventory' => function($query) {
            $query->select('id', 'product_id', 'qty')
                  ->selectRaw('CASE WHEN qty > 0 THEN true ELSE false END as in_stock');
        }]);

        $this->applyActive($query, $request);

        // Select specific columns
        $query->select(['*']);

        // Sorting
        $this->applySorting($query, $request);

        // Searching
        $searchKeys = ['name']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function store(Request $request)
    {
        $data = $this->prepareProductData($request);

        return Product::create($data);
    }

    private function prepareProductData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Product())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['image'] = $this->ftpFileUpload($request, 'image', 'image');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'product');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Product
    {
        return Product::with(['category', 'brand','inventory' => function($query) {
            $query->select('id', 'product_id', 'qty')
                  ->selectRaw('CASE WHEN qty > 0 THEN true ELSE false END as in_stock');
        }])->findOrFail($id);

    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $updateData = $this->prepareProductData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return ! is_null($value);
        });
        $product->update($updateData);

        return $product;
    }

    public function destroy(int $id): bool
    {
        $product = Product::findOrFail($id);
        $product->name .= '_'.Str::random(8);
        $product->deleted_at = now();

        return $product->save();
    }


    public function relatedProducts($request): Collection
    {
        return Product::where('has_related_products', 1)->get();
    }

}
 