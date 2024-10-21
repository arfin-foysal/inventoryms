<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, OrganizationScopedTrait, SoftDeletes;

    protected $fillable = [
        'invoice_no',
        'client_id',
        'sub_total',
        'discount',
        'grand_total',

        'paid_amount',
        'due_amount',
        'payment_status',
        'qr_code',
        'billing_address',
        'shipping_address',
        'is_active',
        'created_by',
        'organization_id',

    ];

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function saleAccountDetails()
    {
        return $this->hasMany(SaleAccountDetail::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
