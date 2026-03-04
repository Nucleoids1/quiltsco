<?php
    namespace Databases;

    class ImagesBinariesRotate extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'posted_on', 'image_id', 'full', 'thumb', 'original'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'images_binaries_rotate';

        public function saveRotation(int $imageId, string $full, string $thumb, string $original): void
        {
            $this->insertArray([
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'image_id' => $imageId,
                'full' => $full,
                'thumb' => $thumb,
                'original' => $original,
            ]);
        }
    }
