<?php
    namespace Databases;

    class Friends extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'friend_id', 'posted_on'];
        protected array $primaryKey = ['user_id', 'friend_id'];
        protected string $tableName = 'friends';

        public function addFriend(int $userId, int $friendId): void
        {
            $this->replaceArray([
                'user_id' => $userId,
                'friend_id' => $friendId,
            ]);
        }

        public function findByUserIdPage(int $userId, int $limit, int $offset): array
        {
            return $this->selectWhere(['user_id' => $userId], 'friend_id', $limit, ['offset' => $offset]);
        }

        public function deleteByUserIdAndFriendId(int $userId, int $friendId): void
        {
            $this->deleteWhere(['user_id' => $userId, 'friend_id' => $friendId]);
        }

        public function findByUserIdAndFriendId(int $userId, int $friendId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'friend_id' => $friendId]);
        }

        public function countByUserId(int $userId): int
        {
            return $this->count(['user_id' => $userId]);
        }

        public function countByFriendId(int $userId): int
        {
            return $this->count(['friend_id' => $userId]);
        }

        public function countAll(): int
        {
            return $this->count([]);
        }

    }
