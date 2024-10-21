<?php

namespace App\Models;

use App\Http\Traits\HelperTrait;
use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use OrganizationScopedTrait;
    use SoftDeletes;
    use HelperTrait;

    protected $fillable = ['name','code','short_description', 'description','barcode','image','unit', 'sku', 'category_id', 'brand_id', 'tags', 'regular_price', 'sale_price', 'is_description_shown_in_invoices', 'has_related_products', 'has_serials',  'is_active', 'created_by', 'organization_id'];

    protected $casts = [
        'has_serials' => 'boolean',
        'is_description_shown_in_invoices' => 'boolean',
        'has_related_products' => 'boolean',
        'is_active' => 'boolean',
        'tags' => 'array',
    ];

    

    public function category()
    {

        return $this->belongsTo(Category::class)->select(['id', 'name', 'image', 'description', 'parent_id', 'is_active']);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->select(['id', 'name', 'image', 'description', 'is_active']);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }


}
