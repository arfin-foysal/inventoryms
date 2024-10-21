<?php

namespace App\Services;

use App\Models\Support;
use App\Http\Traits\HelperTrait;
use App\Models\SupportAccountDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Support::query();

        $query->with('supportType:id,name','product:id,name');
        

        //condition data 
        $this->applyActive($query, $request);

        // Select specific columns
        $query->select(['*']);

        $filters = ['accepted' => '=', 'status' => '=','sale_id' => '=','product_id' => '=','support_type_id' => '=']; // add your filter columns

        $this->applyFilters($query, $request, $filters);

        // Sorting
        $this->applySorting($query, $request);

        // Searching
        $searchKeys = ['task','name']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function store(Request $request)
    {
        $data = $this->prepareSupportData($request);

        $support = Support::create($data);


        return $support;
    }

    private function prepareSupportData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Support())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['attachment'] = $this->ftpFileUpload($request, 'attachment', 'attachment');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'support');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['status'] = 'pending';
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Support
    {
        $support = Support::with(['supportType', 'sale', 'product', 'supportExpenses'])->findOrFail($id);

        $support->employee_ids = explode(',', $support->employee_ids);
        $support->employees = \App\Models\User::whereIn('id', $support->employee_ids)->select('id', 'name')->get();


        return $support;
    }

    public function update(Request $request, int $id)
    {
        $support = Support::findOrFail($id);
        $updateData = $this->prepareSupportData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return !is_null($value);
        });
        $support->update($updateData);

        return $support;
    }

    public function destroy(int $id): bool
    {
        $support = Support::findOrFail($id);

        $support->deleted_at = now();
        return $support->save();
    }

    public function supportList($request): Collection|LengthAwarePaginator|array
    {
        $query = Support::query();
        $query->with('supportType:id,name','product:id,name');
        // Filter by is_active and employee_ids containing auth_id
        $query->where('supports.is_active', true)
              ->whereRaw("FIND_IN_SET(?, supports.employee_ids)", [auth()->id()]);
    
        // Join the users table using FIND_IN_SET
        $query->join('users', function ($join) {
            $join->whereRaw("FIND_IN_SET(users.id, supports.employee_ids)");
        });
    
        // Select specific columns, including employee names
        $query->selectRaw('supports.*, GROUP_CONCAT(users.name) as employee_names')
              ->groupBy('supports.id');
    
        // Sorting
        $this->applySorting($query, $request);
    
        // Searching
        $searchKeys = ['name']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);
    
        // Pagination
        return $this->paginateOrGet($query, $request);
    }
    


    public function taskAccept($request, int $id)
    {
        $support = Support::findOrFail($id);
        $support->accepted = true;
        $support->status = 'on_going';
        $support->accepted_by = auth()->user()->id;
        $support->accepted_date = now();
        $support->save();
        return true;
    }

    public function taskComplete($request, int $id)
    {
        $support = Support::findOrFail($id);
        $support->status = 'completed';
        $support->save();
        return true;
    }

}
