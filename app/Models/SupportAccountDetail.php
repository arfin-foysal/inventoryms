<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportAccountDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_id',
        'support_expense_id',
        'paid_amount',
        'payment_method',
        'description',
        'attachment',
        'transition_id',
        'is_active',
        'is_advance_paid',
        'is_refund_paid',
        'created_by',
        'organization_id',
    ];

    public function support()
    {
        return $this->belongsTo(Support::class, 'support_id');
    }
}
