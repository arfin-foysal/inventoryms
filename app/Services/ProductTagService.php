<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Models\ProductTag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTagService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = ProductTag::query();

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
        // Get the validated data from the request
        $validatedData = $request->validated();

        // Initialize an array to hold data for batch insertion
        $dataToInsert = [];

        // Loop through the 'tags' array
        foreach ($validatedData['tags'] as $tag) {
            $dataToInsert[] = [
                'name' => $tag['name'],
                'description' => $tag['description'] ?? null,
                'is_active' => $tag['is_active'] ?? null,
                'created_by' => auth()->user()->id, // Set the created_by field
                'created_at' => now(), // Set the created_at timestamp
                'updated_at' => now(), // Add updated_at for consistency
            ];
        }

        // Insert all records in one query
        ProductTag::insert($dataToInsert);

        return response()->json(['message' => 'Product tags created successfully'], 201);
    }

    public function show(int $id): ProductTag
    {
        return ProductTag::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $productTag = ProductTag::findOrFail($id);

        $updateData = $request->only($productTag->getFillable());
        $updateData = array_filter($updateData, function ($value) {
            return ! is_null($value);
        });

        $productTag->update($updateData);

        return $productTag;
    }

    public function destroy(int $id): bool
    {
        $productTag = ProductTag::findOrFail($id);
        $productTag->name .= '_'.Str::random(8);
        $productTag->deleted_at = now();

        return $productTag->save();
    }
}
