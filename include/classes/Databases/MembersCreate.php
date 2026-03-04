<?php
    namespace Databases;

    class MembersCreate extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['user_id', 'email', 'cache', 'ip', 'posted_on'];
        protected string $primaryKey = 'email';
        protected string $tableName = 'members_create';

        public function createPendingAccount(string $email, int $userId, string $cache, string $ip): void
        {
            $this->insertArray([
                'email' => $email,
                'user_id' => $userId,
                'cache' => $cache,
                'ip' => $ip,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ]);
        }

        public function findByCache(string $cache): ?array
        {
            return $this->selectWhereRow(['cache' => $cache]);
        }

        public function findValidByCache(string $cache, string $since): ?array
        {
            return $this->selectWhereRow(['cache' => $cache, 'posted_on >' => $since]);
        }

        public function findByEmail(string $email): ?array
        {
            return $this->selectWhereRow(['email' => $email]);
        }

        public function findByUserId(int $userId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId]);
        }

        public function findByUserIdAndCache(int $userId, string $cache): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'cache' => $cache]);
        }

        public function countByEmail(string $email): int
        {
            return $this->count(['email' => $email]);
        }

        public function upsertByEmail(string $email, string $cache, string $ip): void
        {
            $this->replaceArray([
                'email' => $email,
                'cache' => $cache,
                'ip' => $ip,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ]);
        }

        public function deleteByUserId(int $userId): void
        {
            $this->deleteWhere(['user_id' => $userId]);
        }

        public function deleteByEmail(string $email): void
        {
            $this->deleteWhere(['email' => $email]);
        }

        public function deleteByUserIdAndCache(int $userId, string $cache): void
        {
            $this->deleteWhere(['user_id' => $userId, 'cache' => $cache]);
        }

        public function findByUserIdAndCacheAndEmail(int $userId, string $cache, string $email): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'cache' => $cache, 'email' => $email]);
        }

        /**
         * Delete old pending accounts for cleanup; token validity is enforced elsewhere.
         */
        public function deleteOldPendingAccounts(): void
        {
            $this->deleteWhere(['posted_on <' => date('Y-m-d H:i:s', strtotime('-1 day'))]);
        }

        public function countRecentByEmail(string $email, string $since): int
        {
            return $this->count(['email' => $email, 'posted_on >' => $since]);
        }

        public function countRecentByIp(string $ip, string $since): int
        {
            return $this->count(['ip' => $ip, 'posted_on >' => $since]);
        }

    }
