<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleAccount;
use App\Http\Traits\HelperTrait;
use App\Models\SaleAccountDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = SaleAccountDetail::query();

        //condition data 
        $this->applyActive($query, $request);
        $filters = ['sale_id' => '=']; // add your filter columns
    
        // Select specific columns
        $query->select(['*']);

        // Sorting
        $this->applySorting($query, $request);
        
        $this->applyFilters($query, $request, $filters);
        // Searching
        $searchKeys = ['sale_id']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->prepareAccountData($request);
            $sale = Sale::where('id', $request->sale_id)->first();

            if ($sale->payment_status == 'Paid') {
                throw new \Exception('Sale is already paid', 422);
            }

            if ($sale) {
                
                $sale->paid_amount += $request['paid_amount'];
                $sale->due_amount = $sale->grand_total - $sale->paid_amount;
                $sale->payment_status = $sale->due_amount > 0 ? 'Due' : 'Paid';
                $sale->save();
            }
            $account = SaleAccountDetail::create($data);
            DB::commit();
            return $account;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }



    private function prepareAccountData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new SaleAccountDetail())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        //$data['thumbnail'] = $this->ftpFileUpload($request, 'thumbnail', 'account');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'account');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): SaleAccountDetail
    {
        return SaleAccountDetail::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            $account = SaleAccountDetail::findOrFail($id);
            $updateData = $this->prepareAccountData($request, false);

            $saleAccount = Sale::where('id', $account->sale_id)->first();

            if ($saleAccount) {
                $saleAccount->paid_amount = $saleAccount->paid_amount - $account->paid_amount + $updateData['paid_amount'];
                $saleAccount->grand_total = $saleAccount->grand_total - $account->paid_amount + $updateData['paid_amount'];
                $saleAccount->due_amount = $saleAccount->grand_total - $saleAccount->paid_amount;
                $saleAccount->payment_status = $saleAccount->due_amount > 0 ? 'Due' : 'Paid';
                $saleAccount->save();
            }


            $updateData = array_filter($updateData, function ($value) {
                return !is_null($value);
            });
            $account->update($updateData);

            DB::commit();
            return $account;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id): bool
    {
        $account = SaleAccountDetail::findOrFail($id);
        // $account->name .= '_' . Str::random(8);
        $account->deleted_at = now();

        return $account->save();
    }
}
