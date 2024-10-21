<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use OrganizationScopedTrait;
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'email', 'address', 'image', 'company_name', 'customer_source', 'source_details', 'is_active', 'created_by', 'organization_id'];
}
