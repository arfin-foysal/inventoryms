<?php
namespace App\Services;
use App\Exceptions\ErrorMessageException;
use App\Http\Traits\HelperTrait;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\SaleDetail;
use App\Models\StockEntry;
use App\Models\StockEntryDetail;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class InventoryService
{

    use HelperTrait;

    public function inventory(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Inventory::query();

        $query->with(['product:id,name']);
        // Assuming the relation is defined in Inventory
 
        //condition data
        $this->applyActive($query, $request);

        // Select specific columns
        $query->select(['*']);

        // Sorting
        $this->applySorting($query, $request);

        // Searching
        $searchKeys = ['id']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        $query->withSum('stockEntryDetails as total_stock_qty', 'qty');
        $query->withSum('saleDetails as total_sale_qty', 'qty'); 
        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function inventoryCheck(int $id)
    {
        $inventory = Inventory::where('product_id', $id)->where('qty', '>', 0)->first();
        return ['qry' => $inventory->qty];
    }
    
}
