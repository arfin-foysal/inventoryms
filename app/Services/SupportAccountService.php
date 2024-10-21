<?php

namespace App\Services;

use App\Models\SupportAccountDetail;
use App\Http\Traits\HelperTrait;
use App\Models\Support;
use App\Models\SupportExpense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportAccountService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = SupportAccountDetail::query();
        $query->where('support_id', $request->support_id);

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
        // Fetch the Support record once
        $support = Support::findOrFail($request->support_id);

        // Prepare data and create SupportAccountDetail record
        $data = $this->prepareSupportAccountData($request);
        $value = SupportAccountDetail::create($data);
        // Update support values based on payment type
        $support->update([
            'advance_payment' => $request->is_advance_paid ? $request->paid_amount : $support->advance_payment,
            'refund_payment' => $request->is_refund_paid ? $request->paid_amount : $support->refund_payment,
            'total_payment' => $request->is_advance_paid ? $support->total_payment + $request->paid_amount : $support->total_payment - $request->paid_amount,
        ]);

        return $value;
    }


    private function prepareSupportAccountData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new SupportAccountDetail())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['attachment'] = $this->ftpFileUpload($request, 'attachment', 'image');

        if ($isNew) {

            $data['support_id'] = $request->support_id;
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): SupportAccountDetail
    {
        return SupportAccountDetail::with(['support'])->findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $supportAccount = SupportAccountDetail::findOrFail($id);
        $updateData = $this->prepareSupportAccountData($request, false);

        $updateData = array_filter($updateData, function ($value) {
            return !is_null($value);
        });
        $supportAccount->update($updateData);

        return $supportAccount;
    }

    public function destroy(int $id): bool
    {
        $supportAccount = SupportAccountDetail::findOrFail($id);
        $supportAccount->deleted_at = now();

        return $supportAccount->save();
    }



    public function paymentComplete(Request $request, int $id)
    {
        $support = SupportExpense::where('id', $id)->where('is_approved', 1)
            ->first();
        $support->is_completed = 1;
        $support->save();
        return $support;
    }
}
