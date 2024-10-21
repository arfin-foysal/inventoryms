<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockEntryDetail extends Model
{
    use HasFactory, OrganizationScopedTrait, SoftDeletes;

    protected $fillable = [
        'stock_entry_id',
        'product_id',
        'warranty_period',
        'warranty_period_value',
        'unit_price',
        'total_price',
        'qty',
        'description',
        'image',
        'is_active',
        'created_by',
        'organization_id',
    ];

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class, 'stock_entry_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
