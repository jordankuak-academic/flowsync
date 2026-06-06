<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["team_id", "title", "description"])]
class Project extends Model {
    use SoftDeletes;
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ["status"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "team_id" => "integer",
            "title" => "string",
            "description" => "string",
        ];
    }
    
    /**
     * Get the project's dynamic status.
     *
     * @return string
     */
    public function getStatusAttribute(): string {
        $tasks = $this->tasks;
        if ($tasks->isEmpty()) {
            return "inprogress";
        }
        
        $allCompleted = $tasks->every(fn($task) => $task->status === "completed");
        
        return $allCompleted ? "done" : "inprogress";
    }
    
    /**
     * Get the team that owns the project.
     *
     * @return BelongsTo<Team>
     */
    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }
    
    /**
     * Get the tasks that belong to the project.
     *
     * @return HasMany<Task>
     */
    public function tasks(): HasMany {
        return $this->hasMany(Task::class);
    }
}
