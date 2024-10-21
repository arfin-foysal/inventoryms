<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Brand::query();

        $query->when($request->has('is_active'), function ($q) use ($request) {
            $q->where('is_active', $request->input('is_active') === 'true');
        }, function ($q) {
            $q->where('is_active', true);
        });

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
        $data = $this->prepareBrandData($request);

        return Brand::create($data);
    }

    private function prepareBrandData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Brand())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['image'] = $this->ftpFileUpload($request, 'image', 'image');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'brand');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Brand
    {
        return Brand::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {

        $brand = Brand::findOrFail($id);
        $updateData = $this->prepareBrandData($request, false);
        $updateData = array_filter($updateData, function ($value) {
            return ! is_null($value);
        });
        $brand->update($updateData);

        return $brand;
    }

    public function destroy(int $id): bool
    {
        $brand = Brand::findOrFail($id);
        $brand->name .= '_'.Str::random(8);
        $brand->deleted_at = now();

        return $brand->save();
    }
}
