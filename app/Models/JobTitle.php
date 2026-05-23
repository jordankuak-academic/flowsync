<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["team_id", "name", "is_active"])]
class JobTitle extends Model {
    use SoftDeletes;
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "team_id" => "integer",
            "name" => "string",
            "is_active" => "boolean",
        ];
    }
    
    /**
     * Get the team that owns the job title.
     *
     * @return BelongsTo<Team>
     */
    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }
}
