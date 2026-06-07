<?php
namespace App\Services;

use App\Models\Task;
use App\Models\MicroTask;
use Exception;

class TaskService {
    /**
     * Create a new task under a project.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task {
        return Task::create($data);
    }

    /**
     * Update an existing task.
     *
     * @param int $id
     * @param array $data
     * @return Task
     */
    public function updateTask(int $id, array $data): Task {
        $task = Task::findOrFail($id);
        $task->update($data);
        return $task;
    }

    /**
     * Delete a task.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteTask(int $id): ?bool {
        $task = Task::findOrFail($id);
        return $task->delete();
    }

    /**
     * Create a new microtask.
     *
     * @param array $data
     * @return MicroTask
     */
    public function createMicroTask(array $data): MicroTask {
        return MicroTask::create($data);
    }

    /**
     * Update an existing microtask.
     *
     * @param int $id
     * @param array $data
     * @return MicroTask
     */
    public function updateMicroTask(int $id, array $data): MicroTask {
        $microTask = MicroTask::findOrFail($id);
        $microTask->update($data);
        return $microTask;
    }

    /**
     * Delete a microtask.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteMicroTask(int $id): ?bool {
        $microTask = MicroTask::findOrFail($id);
        return $microTask->delete();
    }
}
