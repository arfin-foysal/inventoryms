<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_id',
        'advance_amount',
        'expense_amount',
        'refund_amount',
        'total_amount',
        'attachment',
        'description',
        'is_approved',
        'approved_by',
        'approved_date',
        'is_active',
        'created_by',
        'organization_id',
    ];

    public function SupportHeadDetail()
    {
        return $this->hasMany(SupportHeadDetail::class);
    }

    public function support()
    {
        return $this->belongsTo(Support::class);
    }

    public function accountant()
    {
        return $this->belongsTo(User::class, 'accountant_by');
    }
}
