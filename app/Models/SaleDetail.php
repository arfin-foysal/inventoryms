<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use HasFactory, OrganizationScopedTrait, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'product_id',
        'serial_number',
        'qty',
        'unit_price',
        'total_price',
        'is_active',
        'created_by',
        'organization_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function getSerialsAttribute()
    {
        $serialNumbers = explode(',', $this->serial_number);
        return ProductSerial::whereIn('id', $serialNumbers)->get();
    }
}
