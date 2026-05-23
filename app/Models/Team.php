<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["department_id", "leader_id", "member_id", "is_active"])]
class Team extends Model {
    use SoftDeletes;
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "department_id" => "integer",
            "leader_id" => "integer",
            "member_id" => "integer",
            "is_active" => "boolean",
        ];
    }
    
    /**
     * Get the department that owns the team.
     *
     * @return BelongsTo<Department>
     */
    public function department(): BelongsTo {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Get the leader that owns the team.
     *
     * @return BelongsTo<User>
     */
    public function leader(): BelongsTo {
        return $this->belongsTo(User::class, "leader_id");
    }
    
    /**
     * Get the member that owns the team.
     *
     * @return BelongsTo<User>
     */
    public function member(): BelongsTo {
        return $this->belongsTo(User::class, "member_id");
    }
    
    /**
     * Get the job titles that belong to the team.
     *
     * @return HasMany<JobTitle>
     */
    public function jobTitles(): HasMany {
        return $this->hasMany(JobTitle::class);
    }
    
    /**
     * Get the projects that belong to the team.
     *
     * @return HasMany<Project>
     */
    public function projects(): HasMany {
        return $this->hasMany(Project::class);
    }
}
