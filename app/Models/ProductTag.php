<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTag extends Model
{
    use HasFactory;
    use OrganizationScopedTrait;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'is_active', 'created_by', 'organization_id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
