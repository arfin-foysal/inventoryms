<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Support extends Model
{
    use HasFactory, OrganizationScopedTrait, SoftDeletes;
    protected $fillable = [
        'name', 
        'support_type_id',
        'assign_date',
        'deadline',
        'contact_info',
        'address',
        'sale_id',
        'product_id',
        'employee_ids',
        'status',
        'task',
        'accepted_by',
        'accepted_date',
        'total_payment',
        'advance_payment',
        'refund_payment',
        'attachment',
        'accepted',
        'is_active',
    ];

    public function supportType()
    {
        return $this->belongsTo(SupportType::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supportExpenses()
    {
        return $this->hasOne(SupportExpense::class);
    }
    
    

}
