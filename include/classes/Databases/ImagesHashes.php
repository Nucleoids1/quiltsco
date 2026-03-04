<?php
    namespace Databases;

    class ImagesHashes extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['hash', 'size', 'image_id'];
        protected array $primaryKey = ['hash', 'size'];
        protected string $tableName = 'images_hashes';

        /**
         * Update full image hash and size
         */
        public function updateFullHash(int $imageId, string $hash, int $size): void
        {
            $this->updateWhere(['full_hash' => $hash, 'full_size' => $size], ['image_id' => $imageId]);
        }

        /**
         * Update thumb image hash and size
         */
        public function updateThumbHash(int $imageId, string $hash, int $size): void
        {
            $this->updateWhere(['thumb_hash' => $hash, 'thumb_size' => $size], ['image_id' => $imageId]);
        }

        /**
         * Update original image hash and size
         */
        public function updateOriginalHash(int $imageId, string $hash, int $size): void
        {
            $this->updateWhere(['original_hash' => $hash, 'original_size' => $size], ['image_id' => $imageId]);
        }

        /**
         * Delete image hash data by image ID
         */
        public function deleteImageHash(int $imageId): void
        {
            $this->deleteWhere(['image_id' => $imageId]);
        }

        public function findByFullHashAndSize(string $hash, int $size): ?array
        {
            return $this->selectWhereRow(['full_hash' => $hash, 'full_size' => $size]);
        }

        public function findByThumbHashAndSize(string $hash, int $size): ?array
        {
            return $this->selectWhereRow(['thumb_hash' => $hash, 'thumb_size' => $size]);
        }

        public function findByOriginalHashAndSize(string $hash, int $size): ?array
        {
            return $this->selectWhereRow(['original_hash' => $hash, 'original_size' => $size]);
        }

        public function createRecord(int $imageId, string $fullHash, int $fullSize, string $originalHash, int $originalSize): void
        {
            $this->insertArray(['image_id' => $imageId, 'full_hash' => $fullHash, 'full_size' => $fullSize, 'original_hash' => $originalHash, 'original_size' => $originalSize]);
        }

    }
