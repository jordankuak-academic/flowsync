<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["name", "description", "is_active"])]
class Role extends Model {
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
            "is_active" => "boolean"
        ];
    }
    
    /**
     * Get the users that belong to the role.
     *
     * @return HasMany<User>
     */
    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}
