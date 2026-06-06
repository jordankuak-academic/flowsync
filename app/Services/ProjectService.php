<?php
namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;

class ProjectService {
    /**
     * Get all projects, optionally filtered by dynamic status.
     *
     * @param string|null $status 'inprogress' or 'done'
     * @return Collection
     */
    public function getAllProjects(?string $status = null): Collection {
        $projects = Project::with(["tasks.microTasks", "team"])->get();
        
        if ($status !== null) {
            $projects = $projects->filter(fn($p) => $p->status === $status);
        }
        
        return $projects->values(); // reset keys after filtering for JSON response
    }

    /**
     * Create a new project.
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project {
        // filter out status if provided as it is a dynamic property
        unset($data["status"]);
        return Project::create($data);
    }

    /**
     * Get a project by ID with its tasks and microtasks.
     *
     * @param int $id
     * @return Project
     */
    public function getProjectById(int $id): Project {
        return Project::with(["tasks.microTasks", "team"])->findOrFail($id);
    }

    /**
     * Update an existing project.
     *
     * @param int $id
     * @param array $data
     * @return Project
     */
    public function updateProject(int $id, array $data): Project {
        $project = Project::findOrFail($id);
        unset($data["status"]);
        $project->update($data);
        return $project;
    }

    /**
     * Delete a project.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteProject(int $id): ?bool {
        $project = Project::findOrFail($id);
        return $project->delete();
    }
}
