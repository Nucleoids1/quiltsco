<?php
    namespace Databases;

    class Images extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'user_id', 'width', 'height', 'full_image', 'file_type', 'posted_on', 'commented_on', 'porn', 'category', 'thumb', 'views', 'views_ip'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'images';

        public function findNextImageId(int $imageId): ?array
        {
            return $this->sqlReadRow(
                'SELECT id FROM images WHERE id > ? ORDER BY posted_on LIMIT 1',
                [$imageId]
            );
        }

        public function resetLegacyAutoIncrement(): void
        {
            $this->sqlWrite('ALTER TABLE images PACK_KEYS=1 CHECKSUM=0 DELAY_KEY_WRITE=0 AUTO_INCREMENT=1');
        }

        public function tableExistsInMaster(string $tableName): bool
        {
            $tables = $this->sqlRead('SHOW TABLES', [], ['master' => 'master']);
            foreach ($tables as $table)
            {
                if (in_array($tableName, $table, true))
                {
                    return true;
                }
            }

            return false;
        }

        public function findPreviousImageId(int $imageId): ?array
        {
            return $this->sqlReadRow(
                'SELECT id FROM images WHERE id < ? ORDER BY posted_on DESC LIMIT 1',
                [$imageId]
            );
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function swapDimensions(int $imageId, int $newWidth, int $newHeight): void
        {
            $this->updateWhere(['width' => $newWidth, 'height' => $newHeight], ['id' => $imageId]);
        }

        public function findPageOrderedByPostedOn(int $limit, int $offset): array
        {
            return $this->selectWhere([], 'posted_on DESC', $limit, ['offset' => $offset]);
        }

        public function findThreeBefore(int $id): array
        {
            return $this->selectWhere(['id <' => $id], 'id DESC', 3, ['arrayValue' => 'id']);
        }

        public function findThreeAfter(int $id): array
        {
            return $this->selectWhere(['id >' => $id], 'id', 3, ['arrayValue' => 'id']);
        }

        public function incrementViews(int $imageId): void
        {
            $this->sqlWrite("UPDATE images SET views = views + 1 WHERE id = ?", [$imageId]);
        }

        /**
         * Update image dimensions and file type
         */
        public function updateImageDimensions(int $imageId, int $width, int $height, string $fileType): void
        {
            $this->updateWhere(['width' => $width, 'height' => $height, 'file_type' => $fileType], ['id' => $imageId]);
        }

        public function createForUser(int $userId, string $postedOn): int
        {
            return $this->insertArray(['user_id' => $userId, 'posted_on' => $postedOn]);
        }

        public function deleteById(int $id): void
        {
            $this->deletePrimaryKey($id);
        }

        public function countAll(): int
        {
            return $this->count();
        }

    }
