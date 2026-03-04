<?php
    namespace Databases;

    class MembersOnline extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['sha1', 'sha2', 'closed', 'user_id', 'firston', 'laston', 'page', 'pageviews', 'ip', 'user_agent'];
        protected array $primaryKey = ['sha1', 'sha2'];
        protected string $tableName = 'members_online';

        public function findLatestByUserOrSession(int $userId, string $sha1, string $sha2): ?array
        {
            return $this->sqlReadRow(
                'SELECT * FROM members_online WHERE user_id = ? OR (sha1 = ? AND sha2 = ?) ORDER BY laston DESC LIMIT 1',
                [$userId, $sha1, $sha2]
            );
        }

        public function findBySession(string $sha1, string $sha2): ?array
        {
            return $this->selectWhereRow(['sha1' => $sha1, 'sha2' => $sha2]);
        }

        public function createSession(int $userId, string $sha1, string $sha2, string $firston, int $ip, string $userAgent): int
        {
            return $this->insertArray([
                'sha1' => $sha1,
                'sha2' => $sha2,
                'closed' => '0',
                'user_id' => $userId,
                'firston' => $firston,
                'laston' => $firston,
                'page' => '',
                'pageviews' => 1,
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        public function deleteSession(string $sha1, string $sha2): void
        {
            $this->deleteWhere(['sha1' => $sha1, 'sha2' => $sha2]);
        }

        public function deleteByUserId(int $userId): void
        {
            $this->deleteWhere(['user_id' => $userId]);
        }

        /**
         * Close a login session by setting closed flag
         */
        public function closeSession(string $sha1, string $sha2): void
        {
            $this->updateWhere(['closed' => '1'], ['sha1' => $sha1, 'sha2' => $sha2]);
        }

        /**
         * Update session last activity
         */
        public function updateSessionActivity(string $sha1, string $sha2, string $laston, int $ip, string $userAgent): void
        {
            $this->updateWhere([
                'laston' => $laston,
                'ip' => $ip,
                'user_agent' => $userAgent,
            ], ['sha1' => $sha1, 'sha2' => $sha2]);
        }

        /**
         * Update session last activity with current timestamp
         */
        public function updateSessionActivityNow(string $sha1, string $sha2): void
        {
            $this->updateWhere([
                'laston' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ], ['sha1' => $sha1, 'sha2' => $sha2]);
        }

    }
