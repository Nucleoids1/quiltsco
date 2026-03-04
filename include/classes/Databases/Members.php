<?php
    namespace Databases;

    class Members extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'username', 'email', 'password', 'code', 'sha1', 'main_image_id', 'deleted', 'created_on'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'members';

        public function findByUsernameExcludingUserId(string $username, int $excludeUserId): ?array
        {
            return $this->selectWhereRow([
                'username' => $username,
                'id !=' => $excludeUserId,
            ]);
        }

        public function findActiveByEmailOrUsername(string $emailOrUsername): ?array
        {
            return $this->sqlReadRow(
                "SELECT * FROM members WHERE (email = ? OR username = ?) AND deleted = '0'",
                [$emailOrUsername, $emailOrUsername]
            );
        }

        public function countByUsernamePrefix(string $prefix): int
        {
            $row = $this->sqlReadRow(
                'SELECT COUNT(*) AS num FROM members WHERE username LIKE ?',
                [$prefix . '%']
            );

            return (int)($row['num'] ?? 0);
        }

        public function selectPageByUsername(int $offset, int $limit): array
        {
            return $this->sqlRead(
                'SELECT id, main_image_id FROM members ORDER BY username' . $this->buildLimitOffsetClause($limit, $offset)
            );
        }

        public function selectPageByUsernamePrefix(string $prefix, int $offset, int $limit): array
        {
            return $this->sqlRead(
                'SELECT id, main_image_id FROM members WHERE username LIKE ? ORDER BY username' . $this->buildLimitOffsetClause($limit, $offset),
                [$prefix . '%']
            );
        }

        public function createMember(string $username, string $password, string $email): int
        {
            return $this->insertArray([
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'created_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function findByUsername(string $username): ?array
        {
            return $this->selectWhereRow(['username' => $username]);
        }

        public function findByEmail(string $email): ?array
        {
            return $this->selectWhereRow(['email' => $email]);
        }

        public function findActiveById(int $id): ?array
        {
            return $this->selectWhereRow(['id' => $id, 'deleted' => '0']);
        }

        public function countAll(): int
        {
            return $this->count([]);
        }

        public function countByUsername(string $username): int
        {
            return $this->count(['username' => $username]);
        }

        public function countByEmail(string $email): int
        {
            return $this->count(['email' => $email]);
        }

        public function updateUsername(int $id, string $username): void
        {
            $this->updateWhere(['username' => $username], ['id' => $id]);
        }

        public function updatePassword(int $id, string $password): void
        {
            $this->updateWhere(['password' => $password], ['id' => $id]);
        }

        public function updatePasswordByEmail(string $email, string $password): void
        {
            $this->updateWhere(['password' => $password], ['email' => $email]);
        }

        public function updateEmail(int $id, string $email): void
        {
            $this->updateWhere(['email' => $email], ['id' => $id]);
        }

        public function setMainImage(int $userId, int $imageId): void
        {
            $this->updateWhere(['main_image_id' => $imageId], ['id' => $userId]);
        }


    }
