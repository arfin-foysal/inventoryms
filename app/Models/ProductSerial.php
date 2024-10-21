<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSerial extends Model
{
    use HasFactory, OrganizationScopedTrait, SoftDeletes;

    protected $fillable = [
        'product_id',
        'stock_entry_id',
        'serial_number',
        'description',
        'sold',
        'is_active',
        'created_by',
        'organization_id',
    ];
}
