<?php
    namespace Databases;

    class Tiles extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['tile_id', 'quilt_id', 'matrix_x', 'matrix_y', 'user_id', 'comment', 'started_on', 'posted_on', 'seconds', 'borders', 'visibility', 'deleted', 'views', 'cache_display', 'data_tile'];
        protected string $primaryKey = 'tile_id';
        protected string $tableName = 'tiles';


        public function findByQuiltAndMatrixNotDeleted(int $quiltId, int $x, int $y): ?array
        {
            return $this->selectWhereRow([
                'quilt_id' => $quiltId,
                'matrix_x' => $x,
                'matrix_y' => $y,
                'deleted' => 0,
            ]);
        }

        public function countByQuilt(int $quiltId): int
        {
            return $this->count([
                'quilt_id' => $quiltId,
            ]);
        }

        public function countByQuiltNotDeleted(int $quiltId): int
        {
            return $this->count([
                'quilt_id' => $quiltId,
                'deleted' => 0,
            ]);
        }

        public function selectRandomVisibleByQuilt(int $quiltId): array
        {
            return $this->sqlRead(
                'SELECT * FROM tiles WHERE quilt_id = ? AND deleted = ? ORDER BY RAND()',
                [$quiltId, '0']
            );
        }

        public function selectDistinctUserIdsByQuilt(int $quiltId): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(user_id) AS user_id FROM tiles WHERE quilt_id = ? AND deleted = ?',
                [$quiltId, 0]
            );
        }

        public function selectPendingApprovalsByUser(int $userId, int $offset, int $limit): array
        {
            return $this->sqlRead(
                "SELECT * FROM tiles WHERE quilt_id IN (SELECT quilt_id FROM quilts_permissions WHERE user_id = ?) AND visibility = '-1' ORDER BY tile_id DESC" . $this->buildLimitOffsetClause($limit, $offset),
                [$userId]
            );
        }


        public function sumVisibleSecondsByQuilt(int $quiltId): int
        {
            $row = $this->sqlReadRow(
                'SELECT SUM(seconds) AS count FROM tiles WHERE quilt_id = ? AND visibility >= ? AND deleted = ?',
                [$quiltId, '0', '0']
            );

            return (int)($row['count'] ?? 0);
        }

        public function countDistinctVisibleUsersByQuilt(int $quiltId): int
        {
            $row = $this->sqlReadRow(
                'SELECT COUNT(DISTINCT(user_id)) AS count FROM tiles WHERE quilt_id = ? AND visibility >= ? AND deleted = ?',
                [$quiltId, '0', '0']
            );

            return (int)($row['count'] ?? 0);
        }

        public function sumVisibleSecondsByUser(int $userId): int
        {
            $row = $this->sqlReadRow(
                'SELECT SUM(seconds) AS count FROM tiles WHERE user_id = ? AND deleted = ? AND visibility >= ?',
                [$userId, '0', '0']
            );

            return (int)($row['count'] ?? 0);
        }

        public function countPendingApprovalsByUser(int $userId): int
        {
            $row = $this->sqlReadRow(
                "SELECT COUNT(*) AS count FROM tiles WHERE quilt_id IN (SELECT quilt_id FROM quilts_permissions WHERE user_id = ?) AND visibility = '-1'",
                [$userId]
            );

            return (int)($row['count'] ?? 0);
        }

        public function submitTile(
            int $quiltId,
            int $x,
            int $y,
            int $userId,
            string $comment,
            string $startedOn,
            int $seconds,
            string $borders,
            int $visibility,
            string $dataTile
        ): int {
            return $this->insertArray([
                'quilt_id' => $quiltId,
                'matrix_x' => $x,
                'matrix_y' => $y,
                'user_id' => $userId,
                'comment' => $comment,
                'started_on' => $startedOn,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'seconds' => $seconds,
                'borders' => $borders,
                'visibility' => $visibility,
                'data_tile' => $dataTile,
            ]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function findPendingById(int $tileId): ?array
        {
            return $this->selectWhereRow([
                'tile_id' => $tileId,
                'visibility' => '-1',
            ]);
        }

        public function findByTileIdAndUserId(int $tileId, int $userId): ?array
        {
            return $this->selectWhereRow([
                'tile_id' => $tileId,
                'user_id' => $userId,
            ]);
        }

        public function findByQuiltAndMatrixVisible(int $quiltId, int $x, int $y): ?array
        {
            return $this->selectWhereRow([
                'quilt_id' => $quiltId,
                'matrix_x' => $x,
                'matrix_y' => $y,
                'visibility >=' => 0,
                'deleted' => 0,
            ]);
        }

        public function countVisibleByQuilt(int $quiltId): int
        {
            return $this->count([
                'quilt_id' => $quiltId,
                'visibility >=' => '0',
                'deleted' => '0',
            ]);
        }

        public function countByUserNotDeleted(int $userId): int
        {
            return $this->count([
                'user_id' => $userId,
                'deleted' => 0,
            ]);
        }

        public function countVisibleByUser(int $userId): int
        {
            return $this->count([
                'user_id' => $userId,
                'deleted' => '0',
                'visibility >=' => '0',
            ]);
        }

        public function countByQuiltAndUserNotDeleted(int $quiltId, int $userId): int
        {
            return $this->count([
                'quilt_id' => $quiltId,
                'user_id' => $userId,
                'deleted' => 0,
            ]);
        }

        public function approveTile(int $tileId): void
        {
            $this->updateWhere(['visibility' => '0'], ['tile_id' => $tileId]);
        }

        public function rejectTile(int $tileId): void
        {
            $this->updateWhere(['visibility' => '0', 'deleted' => '1'], ['tile_id' => $tileId]);
        }

        public function updateComment(int $tileId, string $comment): void
        {
            $this->updateWhere(['comment' => $comment], ['tile_id' => $tileId]);
        }

        public function softDelete(int $tileId): void
        {
            $this->updateWhere(['deleted' => '1'], ['tile_id' => $tileId]);
        }

        public function setVisibility(int $tileId, int $visibility): void
        {
            $this->updateWhere(['visibility' => $visibility], ['tile_id' => $tileId]);
        }

        public function setCacheDisplay(int $tileId, int $cacheDisplay): void
        {
            $this->updateWhere(['cache_display' => $cacheDisplay], ['tile_id' => $tileId]);
        }

        public function selectCachedByUser(int $userId, int $limit = 0, int $offset = 0): array
        {
            return $this->selectWhere(['user_id' => $userId, 'deleted' => '0', 'cache_display' => '1'], 'tile_id DESC', $limit, ['offset' => $offset]);
        }

        public function selectNonCachedByUser(int $userId): array
        {
            return $this->selectWhere(['user_id' => $userId, 'deleted' => '0', 'cache_display' => '0']);
        }

        public function selectByUserNotDeletedPage(int $userId, int $limit, int $offset): array
        {
            return $this->selectWhere(
                ['user_id' => $userId, 'deleted' => '0'],
                'tile_id DESC',
                $limit,
                ['offset' => $offset]
            );
        }

        public function incrementViews(int $tileId): void
        {
            $this->sqlWrite('UPDATE tiles SET views = views + 1 WHERE tile_id = ?', [$tileId]);
        }


    }
