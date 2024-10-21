<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleAccountDetail extends Model
{
    use HasFactory, OrganizationScopedTrait, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'client_id',
        'paid_amount',
        'payment_method',
        'description',
        'attachment',
        'transition_id',
        'is_active',
        'created_by',
        'organization_id',

    ];
}
