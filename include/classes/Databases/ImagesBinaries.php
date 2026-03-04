<?php
    namespace Databases;

    class ImagesBinaries extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['image_id', 'full', 'thumb', 'original'];
        protected string $primaryKey = 'image_id';
        protected string $tableName;

        public function __construct(string $binariesPath)
        {
            if (!preg_match('/^\d{8}$/', $binariesPath))
            {
                throw new \InvalidArgumentException('Invalid binaries path: ' . $binariesPath);
            }
            $this->tableName = 'images_binaries_' . $binariesPath;
            parent::__construct();
        }

        public function tableName(): string
        {
            return $this->tableName;
        }

        public function createTable()
        {
            return $this->sqlWrite('CREATE TABLE IF NOT EXISTS `' . $this->tableName . '` (`image_id` mediumint(8) unsigned NOT NULL, `full` mediumblob NOT NULL, `thumb` mediumblob NOT NULL, `original` mediumblob NOT NULL, PRIMARY KEY (`image_id`)) ENGINE = MYISAM DEFAULT CHARSET = utf8mb4');
        }

        public function findByImageId(int $imageId): ?array
        {
            return $this->selectWhereRow(['image_id' => $imageId]);
        }

        public function findByImageIdWithFields(int $imageId, string $fields): ?array
        {
            return $this->selectWhereRow(['image_id' => $imageId], '', ['fields' => $fields]);
        }

        /**
         * Update full image data
         */
        public function updateFullImageData(int $imageId, string $fullData): void
        {
            $this->updateWhere(['full' => $fullData], ['image_id' => $imageId]);
        }

        /**
         * Update thumb image data
         */
        public function updateThumbImageData(int $imageId, string $thumbData): void
        {
            $this->updateWhere(['thumb' => $thumbData], ['image_id' => $imageId]);
        }

        /**
         * Update original image data
         */
        public function updateOriginalImageData(int $imageId, string $originalData): void
        {
            $this->updateWhere(['original' => $originalData], ['image_id' => $imageId]);
        }

        /**
         * Delete image data by image ID
         */
        public function deleteImageData(int $imageId): void
        {
            $this->deleteWhere(['image_id' => $imageId]);
        }

        public function createRecord(int $imageId): void
        {
            $this->insertArray(['image_id' => $imageId]);
        }

    }
