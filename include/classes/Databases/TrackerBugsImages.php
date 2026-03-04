<?php
    namespace Databases;

    class TrackerBugsImages extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['image_id', 'tracker_id', 'user_id', 'posted_on', 'description'];
        protected array $primaryKey = ['image_id', 'tracker_id'];
        protected string $tableName = 'tracker_bugs_images';

        public function attachImage(int $imageId, int $trackerId, int $userId, string $description): void
        {
            $this->insertArray([
                'image_id' => $imageId,
                'tracker_id' => $trackerId,
                'user_id' => $userId,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'description' => $description,
            ]);
        }

        public function deleteByTrackerId(int $trackerId): void
        {
            $this->deleteWhere(['tracker_id' => $trackerId]);
        }

        public function findByTrackerId(int $trackerId): array
        {
            return $this->selectWhere(['tracker_id' => $trackerId]);
        }

        public function findOneByImageIdAndTrackerId(int $imageId, int $trackerId): ?array
        {
            return $this->selectWhereRow(['image_id' => $imageId, 'tracker_id' => $trackerId]);
        }

        public function deleteByImageIdAndTrackerId(int $imageId, int $trackerId): void
        {
            $this->deleteWhere(['image_id' => $imageId, 'tracker_id' => $trackerId]);
        }

    }
