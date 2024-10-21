<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockEntry extends Model
{
    use HasFactory;
    use OrganizationScopedTrait;
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'invoice_number',
        'received_date',
        'price',
        'image',
        'discount_price',
        'total_price',
        'payment_status',
        'is_active',
        'created_by',
        'organization_id',

    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function StockEntryDetail()
    {
        return $this->hasMany(StockEntryDetail::class, 'stock_entry_id');
    }
}
