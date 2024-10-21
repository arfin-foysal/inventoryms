<?php

namespace App\Services;

use App\Models\SupportType;
use App\Http\Traits\HelperTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportTypeService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = SupportType::query();

        //condition data 
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
        $data = $this->prepareSupportTypeData($request);

        return SupportType::create($data);
    }

    private function prepareSupportTypeData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new SupportType())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        //$data['thumbnail'] = $this->ftpFileUpload($request, 'thumbnail', 'supportType');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'supportType');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): SupportType
    {
        return SupportType::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $supportType = SupportType::findOrFail($id);
        $updateData = $this->prepareSupportTypeData($request, false);
        
         $updateData = array_filter($updateData, function ($value) {
            return !is_null($value);
        });
        $supportType->update($updateData);

        return $supportType;
    }

    public function destroy(int $id): bool
    {
        $supportType = SupportType::findOrFail($id);
        $supportType->name .= '_' . Str::random(8);
        $supportType->deleted_at = now();

        return $supportType->save();
    }
}
