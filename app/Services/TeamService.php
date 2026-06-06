<?php
namespace App\Services;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamService {
    /**
     * Get all teams.
     *
     * @return Collection<Team>
     */
    public function getAllTeams(): Collection {
        return Team::with(["department", "leader", "member"])->get();
    }

    /**
     * Create a new team.
     *
     * @param array $data
     * @return Team
     */
    public function createTeam(array $data): Team {
        return Team::create($data);
    }

    /**
     * Get a team by ID.
     *
     * @param int $id
     * @return Team
     */
    public function getTeamById(int $id): Team {
        return Team::with(["department", "leader", "member"])->findOrFail($id);
    }

    /**
     * Update an existing team.
     *
     * @param int $id
     * @param array $data
     * @return Team
     */
    public function updateTeam(int $id, array $data): Team {
        $team = Team::findOrFail($id);
        $team->update($data);
        return $team;
    }

    /**
     * Delete a team.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteTeam(int $id): ?bool {
        $team = Team::findOrFail($id);
        return $team->delete();
    }
}
