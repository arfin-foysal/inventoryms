<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, OrganizationScopedTrait, SoftDeletes;

    protected $fillable = [
        'product_id',
        'qty',
        'transaction_qty',
        'is_active',
        'created_by',
        'organization_id',
    ];

      protected $casts = [
        'total_stock_qty' => 'integer',
        'total_sale_qty' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function stockEntryDetails()
{
    return $this->hasMany(StockEntryDetail::class, 'product_id', 'product_id');
}

public function saleDetails()
{
    return $this->hasMany(SaleDetail::class, 'product_id', 'product_id');
}
}
