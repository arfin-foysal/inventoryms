<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Client::query();

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
        $data = $this->prepareClientData($request);

        return Client::create($data);
    }

    private function prepareClientData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Client())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['image'] = $this->ftpFileUpload($request, 'image', 'image');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'client');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Client
    {
        return Client::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $client = Client::findOrFail($id);
        $updateData = $this->prepareClientData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return ! is_null($value);
        });
        $client->update($updateData);

        return $client;
    }

    public function destroy(int $id): bool
    {
        $client = Client::findOrFail($id);
        $client->name .= '_'.Str::random(8);
        $client->deleted_at = now();

        return $client->save();
    }
}
