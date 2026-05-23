<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["project_id", "title", "assignees", "due_date", "priority", "status"])]
class Task extends Model {
    use SoftDeletes;
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "project_id" => "integer",
            "title" => "string",
            "assignees" => "array",
            "due_date" => "date",
            "priority" => "string",
            "status" => "string",
        ];
    }
    
    /**
     * Get the project that owns the task.
     *
     * @return BelongsTo<Project>
     */
    public function project(): BelongsTo {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Get the micro tasks that belong to the task.
     *
     * @return HasMany<MicroTask>
     */
    public function microTasks(): HasMany {
        return $this->hasMany(MicroTask::class);
    }
}
