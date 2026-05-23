<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["task_id", "title", "assignees", "due_date", "priority", "status"])]
class MicroTask extends Model {
    use SoftDeletes;
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "task_id" => "integer",
            "title" => "string",
            "assignees" => "array",
            "due_date" => "date",
            "priority" => "string",
            "status" => "string",
        ];
    }
    
    /**
     * Get the task that owns the micro task.
     *
     * @return BelongsTo<Task>
     */
    public function task(): BelongsTo {
        return $this->belongsTo(Task::class);
    }
}
