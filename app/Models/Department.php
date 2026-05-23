<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["name", "description", "code", "is_active"])]
class Department extends Model {
    use SoftDeletes;
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "name" => "string",
            "description" => "string",
            "code" => "string",
            "is_active" => "boolean",
        ];
    }
    
    /**
     * Get the teams that belong to the department.
     *
     * @return HasMany<Team>
     */
    public function teams(): HasMany {
        return $this->hasMany(Team::class);
    }
}
