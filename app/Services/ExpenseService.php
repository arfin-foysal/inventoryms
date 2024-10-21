<?php

namespace App\Services;

use App\Models\SupportExpense;
use App\Http\Traits\HelperTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = SupportExpense::query();

        $query->with('support:id');


        //condition data 
        $this->applyActive($query, $request);

        // Select specific columns
        $query->select(['*']);

        // Sorting
        $this->applySorting($query, $request);

        // Searching
        $searchKeys = ['support_id']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function store(Request $request)
    {
        $data = $this->prepareExpenseData($request);

        $supportExpense = SupportExpense::create($data);

        if ($request->head_details) {
            $supportExpense->SupportHeadDetail()->createMany($request->head_details);
        }

        return $supportExpense;
    }

    private function prepareExpenseData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new SupportExpense())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['attachment'] = $this->ftpFileUpload($request, 'attachment', 'image');


        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): SupportExpense
    {
        return SupportExpense::with(['SupportHeadDetail', 'support', 'accountant'])->findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $expense = SupportExpense::findOrFail($id);
        $updateData = $this->prepareExpenseData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return !is_null($value);
        });
        $expense->update($updateData);

        if ($request->head_details) {
            $expense->SupportHeadDetail()->delete();
            $expense->SupportHeadDetail()->createMany($request->head_details);
        }
        return $expense;
    }

    public function destroy(int $id): bool
    {
        $expense = SupportExpense::findOrFail($id);
        $expense->deleted_at = now();

        return $expense->save();
    }


    public function approve(int $id)
    {
        $support = SupportExpense::findOrFail($id);
        $support->is_approved = 1;
        $support->approved_by = auth()->user()->id;
        $support->approved_date = now();
        $support->save();
        return true;
    }
}
