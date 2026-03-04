<?php
    namespace Databases;

    class QuiltsPermissions extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'quilt_id', 'permission', 'active'];
        protected array $primaryKey = ['user_id', 'quilt_id'];
        protected string $tableName = 'quilts_permissions';


        public function hasPermissionByUserAndQuilt(int $userId, int $quiltId): bool
        {
            return (bool)$this->count([
                'user_id' => $userId,
                'quilt_id' => $quiltId,
            ]);
        }

        public function hasRootPermissionByUserAndQuilt(int $userId, int $quiltId): bool
        {
            return (bool)$this->count([
                'user_id' => $userId,
                'quilt_id' => $quiltId,
                'permission' => 'root',
            ]);
        }

        public function hasActivePermissionByUserAndQuilt(int $userId, int $quiltId): bool
        {
            return (bool)$this->count([
                'user_id' => $userId,
                'quilt_id' => $quiltId,
                'active' => '1',
            ]);
        }

        public function selectWithMembersByQuilt(int $quiltId): array
        {
            return $this->sqlRead(
                'SELECT * FROM quilts_permissions INNER JOIN members ON quilts_permissions.user_id = members.id WHERE quilts_permissions.quilt_id = ? ORDER BY quilts_permissions.permission DESC, members.username',
                [$quiltId]
            );
        }

        public function grantPermission(int $userId, int $quiltId, string $permission): void
        {
            $this->insertArray([
                'user_id' => $userId,
                'quilt_id' => $quiltId,
                'permission' => $permission,
            ]);
        }

        public function findByUserAndQuilt(int $userId, int $quiltId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'quilt_id' => $quiltId]);
        }

        public function findRootByUserAndQuilt(int $userId, int $quiltId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'quilt_id' => $quiltId, 'permission' => 'root']);
        }

        public function activate(int $userId, int $quiltId): void
        {
            $this->updateWhere(['active' => '1'], ['user_id' => $userId, 'quilt_id' => $quiltId]);
        }

        public function deactivate(int $userId, int $quiltId): void
        {
            $this->updateWhere(['active' => '0'], ['user_id' => $userId, 'quilt_id' => $quiltId]);
        }

        public function deleteByUserAndQuilt(int $userId, int $quiltId): void
        {
            $this->deleteWhere(['user_id' => $userId, 'quilt_id' => $quiltId]);
        }

        public function countByUser(int $userId): int
        {
            return $this->count(['user_id' => $userId]);
        }

        public function countRootByUserAndQuilt(int $userId, int $quiltId): int
        {
            return $this->count(['user_id' => $userId, 'quilt_id' => $quiltId, 'permission' => 'root']);
        }

        public function selectByUserAndQuilt(int $userId, int $quiltId): array
        {
            return $this->selectWhere(['user_id' => $userId, 'quilt_id' => $quiltId]);
        }

        public function selectByUserOrderedByQuiltDesc(int $userId): array
        {
            return $this->selectWhere(['user_id' => $userId], 'quilt_id DESC');
        }

    }
