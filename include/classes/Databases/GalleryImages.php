<?php
    namespace Databases;

    class GalleryImages extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'image_id', 'sort_id', 'posted_on', 'commented_on', 'folder', 'description'];
        protected array $primaryKey = ['image_id', 'user_id'];
        protected string $tableName = 'gallery_images';

        public function addToGallery(int $imageId, int $userId): void
        {
            $this->replaceArray([
                'image_id' => $imageId,
                'user_id' => $userId,
            ]);
        }

        public function findNextImageIdForUser(int $userId, int $imageId): ?array
        {
            return $this->sqlReadRow(
                'SELECT image_id AS id FROM gallery_images WHERE user_id = ? AND image_id > ? ORDER BY posted_on LIMIT 1',
                [$userId, $imageId]
            );
        }

        public function findPreviousImageIdForUser(int $userId, int $imageId): ?array
        {
            return $this->sqlReadRow(
                'SELECT image_id AS id FROM gallery_images WHERE user_id = ? AND image_id < ? ORDER BY posted_on DESC LIMIT 1',
                [$userId, $imageId]
            );
        }

        public function findByUserId(int $userId): array
        {
            return $this->selectWhere(['user_id' => $userId]);
        }

        public function findRecentByUserId(int $userId, int $limit): array
        {
            return $this->selectWhere(['user_id' => $userId], 'image_id DESC', $limit);
        }

        public function findPageByUserId(int $userId, int $limit, int $offset): array
        {
            return $this->selectWhere(['user_id' => $userId], '', $limit, ['offset' => $offset]);
        }

        public function findPageByUserIdPostedOn(int $userId, int $limit, int $offset): array
        {
            return $this->selectWhere(['user_id' => $userId], 'posted_on DESC', $limit, ['offset' => $offset]);
        }

        public function findByImageIdAndUserId(int $imageId, int $userId): array
        {
            return $this->selectWhere(['image_id' => $imageId, 'user_id' => $userId]);
        }

        public function deleteByImageId(int $imageId): void
        {
            $this->deleteWhere(['image_id' => $imageId]);
        }

        public function deleteByImageIdAndUserId(int $imageId, int $userId): void
        {
            $this->deleteWhere(['image_id' => $imageId, 'user_id' => $userId]);
        }

        public function findByImageId(int $imageId): ?array
        {
            return $this->selectWhereRow(['image_id' => $imageId]);
        }

        public function findOneByImageIdAndUserId(int $imageId, int $userId): ?array
        {
            return $this->selectWhereRow(['image_id' => $imageId, 'user_id' => $userId]);
        }

        public function countByUserId(int $userId): int
        {
            return $this->count(['user_id' => $userId]);
        }

    }
