<?php
namespace App\Utilities\Validation;

use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

trait HasSoftDelete {
    /**
     * Check If The Model Is Soft Delete Model.
     *
     * @param object|string $model The Model To Check If It Is Soft Delete Model
     * @throws Exception If Error Occurred While Checking If Model Is Soft Delete Model
     * @return bool True If Model Is Soft Delete Model, False Otherwise
     */
    protected function isSoftDeleteModel($model): bool {
        try {
            $traits = class_uses_recursive($model);
            return \in_array(SoftDeletes::class, $traits);
        } catch (Exception $exception) {
            throw new Exception("Error While Checking If Model Is Soft Delete Model: {$exception->getMessage()}");
        }
    }

    /**
     * Check If The Table Has Deleted At Column.
     *
     * @param string $table The Table To Check If It Has Deleted At Column
     * @throws Exception If Error Occurred While Checking If Table Has Deleted At Column
     * @return bool True If Table Has Deleted At Column, False Otherwise
     */
    protected function hasDeletedAtColumn($table): bool {
        try {
            return Schema::hasColumn($table, 'deleted_at');
        } catch (Exception $exception) {
            throw new Exception("Error While Checking If Table Has Deleted At Column: {$exception->getMessage()}");
        }
    }
}
