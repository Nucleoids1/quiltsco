<?php
    namespace Databases;

    class TilesPending extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['quilt_id', 'matrix_x', 'matrix_y', 'user_id', 'started_on', 'due_date', 'borders'];
        protected array $primaryKey = ['quilt_id', 'matrix_x', 'matrix_y'];
        protected string $tableName = 'tiles_pending';

        public function findByQuiltAndMatrix(int $quiltId, int $x, int $y): ?array
        {
            return $this->selectWhereRow([
                'quilt_id' => $quiltId,
                'matrix_x' => $x,
                'matrix_y' => $y,
            ]);
        }

        public function countByQuilt(int $quiltId): int
        {
            return $this->count(['quilt_id' => $quiltId]);
        }

        public function countByQuiltAndUser(int $quiltId, int $userId): int
        {
            return $this->count(['quilt_id' => $quiltId, 'user_id' => $userId]);
        }

        public function checkoutTile(int $quiltId, int $x, int $y, int $userId, int $timelimit, string $borders): void
        {
            $datetime = date('Y-m-d H:i:s', server('REQUEST_TIME'));
            $dueDate = date('Y-m-d H:i:s', mktime(
                intval(substr($datetime, 11, 2)),
                intval(substr($datetime, 14, 2)),
                intval(substr($datetime, 17, 2)) + $timelimit,
                intval(substr($datetime, 5, 2)),
                intval(substr($datetime, 8, 2)),
                intval(substr($datetime, 0, 4))
            ));

            $this->insertArray([
                'quilt_id' => $quiltId,
                'matrix_x' => $x,
                'matrix_y' => $y,
                'user_id' => $userId,
                'started_on' => $datetime,
                'due_date' => $dueDate,
                'borders' => $borders,
            ]);
        }

        public function deleteByQuiltAndMatrixAndUserId(int $quiltId, int $matrixX, int $matrixY, int $userId): void
        {
            $this->deleteWhere(['quilt_id' => $quiltId, 'matrix_x' => $matrixX, 'matrix_y' => $matrixY, 'user_id' => $userId]);
        }

        public function findByQuiltAndMatrixAndUser(int $quiltId, int $x, int $y, int $userId): ?array
        {
            return $this->selectWhereRow(['quilt_id' => $quiltId, 'matrix_x' => $x, 'matrix_y' => $y, 'user_id' => $userId]);
        }

        public function findByQuiltAndUser(int $quiltId, int $userId): ?array
        {
            return $this->selectWhereRow(['quilt_id' => $quiltId, 'user_id' => $userId]);
        }

        public function findByUserIdPage(int $userId, int $offset): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId], 'due_date DESC', ['offset' => $offset]);
        }

        public function deleteExpired(): void
        {
            $this->sqlWrite("DELETE FROM tiles_pending WHERE due_date < ?", [date('Y-m-d H:i:s', server('REQUEST_TIME'))]);
        }

        public function countByUserId(int $userId): int
        {
            return $this->count(['user_id' => $userId]);
        }

    }
