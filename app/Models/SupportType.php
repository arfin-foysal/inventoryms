<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use OrganizationScopedTrait;


    protected $fillable = ['name', 'description', 'is_active', 'created_by', 'organization_id'];
}
