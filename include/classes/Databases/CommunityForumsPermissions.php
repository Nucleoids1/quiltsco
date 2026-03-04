<?php
    namespace Databases;

    class CommunityForumsPermissions extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['forum_id', 'user_id', 'permission'];
        protected array $primaryKey = ['forum_id', 'user_id', 'permission'];
        protected string $tableName = 'community_forums_permissions';



        public function findByForumAndUser(int $forumId, int $userId): ?array
        {
            return $this->selectWhereRow([
                'forum_id' => $forumId,
                'user_id' => $userId,
            ]);
        }

        public function selectDistinctUserIdsByForum(int $forumId): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(user_id) AS user_id FROM community_forums_permissions WHERE forum_id = ?',
                [$forumId]
            );
        }

        public function selectByForumAndUser(int $forumId, int $userId): array
        {
            return $this->selectWhere([
                'forum_id' => $forumId,
                'user_id' => $userId,
            ], 'permission');
        }

        public function selectDistinctPermissions(): array
        {
            return $this->sqlRead('SELECT DISTINCT(permission) AS permission FROM community_forums_permissions ORDER BY permission');
        }

        public function selectDistinctPermissionsExcept(string $permission): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(permission) AS permission FROM community_forums_permissions WHERE permission != ?',
                [$permission]
            );
        }

        public function hasPermissionByForumAndUser(int $forumId, int $userId, string $permission): bool
        {
            return (bool)$this->count([
                'forum_id' => $forumId,
                'user_id' => $userId,
                'permission' => $permission,
            ]);
        }

        public function grantPermission(int $forumId, int $userId, string $permission): void
        {
            $this->insertArray([
                'forum_id' => $forumId,
                'user_id' => $userId,
                'permission' => $permission,
            ]);
        }

        public function grantPermissionOrUpdate(int $forumId, int $userId, string $permission): void
        {
            $this->insertArrayUpdateRow(
                [
                    'forum_id' => $forumId,
                    'user_id' => $userId,
                    'permission' => $permission,
                ],
                ['permission' => $permission]
            );
        }

        public function deleteByForumAndUser(int $forumId, int $userId): void
        {
            $this->deleteWhere([
                'forum_id' => $forumId,
                'user_id' => $userId,
            ]);
        }
    }
