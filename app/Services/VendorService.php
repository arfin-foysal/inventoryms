<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Vendor::query();

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
        $data = $this->prepareVendorData($request);

        return Vendor::create($data);
    }

    private function prepareVendorData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Vendor())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['image'] = $this->ftpFileUpload($request, 'image', 'image');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'vendor');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Vendor
    {
        return Vendor::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $vendor = Vendor::findOrFail($id);
        $updateData = $this->prepareVendorData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return ! is_null($value);
        });
        $vendor->update($updateData);

        return $vendor;
    }

    public function destroy(int $id): bool
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->name .= '_'.Str::random(8);
        $vendor->deleted_at = now();

        return $vendor->save();
    }
}
