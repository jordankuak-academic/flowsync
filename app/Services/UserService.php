<?php
namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService {
    /**
     * Get all users.
     *
     * @return Collection<User>
     */
    public function getAllUsers(): Collection {
        return User::with("role")->get();
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function createUser(array $data): User {
        if (isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        }
        return User::create($data);
    }

    /**
     * Get a user by ID.
     *
     * @param int $id
     * @return User
     */
    public function getUserById(int $id): User {
        return User::with("role")->findOrFail($id);
    }

    /**
     * Update an existing user.
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateUser(int $id, array $data): User {
        $user = User::findOrFail($id);
        if (isset($data["password"]) && !empty($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        } else {
            unset($data["password"]);
        }
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteUser(int $id): ?bool {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
