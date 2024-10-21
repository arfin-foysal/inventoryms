<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportHeadDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_expense_id',
        'expense_head',
        'amount',
        'attachment',
        'is_active',
        'created_by',
        'organization_id',
    ];
}
