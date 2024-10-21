<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory;
    use OrganizationScopedTrait;
    use SoftDeletes;

    protected $fillable = ['company_name', 'email', 'number', 'image', 'name', 'website', 'description', 'address', 'is_active', 'organization_id', 'created_by'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
