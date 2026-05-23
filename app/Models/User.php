<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(["role_id", "staff_id", "name", "username", "email", "password", "job_title", "ic_number", "contact", "is_active"])]
#[Hidden(["password", "remember_token"])]
class User extends Authenticatable {
    use Notifiable, SoftDeletes;
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "role_id" => "integer",
            "staff_id" => "string",
            "name" => "string",
            "username" => "string",
            "email" => "string",
            "job_title" => "string",
            "ic_number" => "string",
            "contact" => "string",
            "is_active" => "boolean",
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }
    
    /**
     * Get the role that owns the user.
     *
     * @return BelongsTo<Role>
     */
    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }
    
    /**
     * Get the team that owns the user as a leader.
     *
     * @return HasOne<Team>
     */
    public function teamLeader(): HasOne {
        return $this->hasOne(Team::class, "leader_id");
    }
    
    /**
     * Get the team that owns the user as a member.
     *
     * @return HasOne<Team>
     */
    public function teamMember(): HasOne {
        return $this->hasOne(Team::class, "member_id");
    }
}
