<?php
    namespace Databases;

    class ImagesRating extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'image_id', 'posted_on', 'vote'];
        protected array $primaryKey = ['user_id', 'image_id'];
        protected string $tableName = 'images_rating';

        public function upsertVote(int $userId, int $imageId, int $vote): void
        {
            $this->replaceArray([
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'user_id' => $userId,
                'vote' => $vote,
                'image_id' => $imageId,
            ]);
        }

        public function findByUserIdAndImageId(int $userId, int $imageId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'image_id' => $imageId]);
        }

        public function countGoodByImageId(int $imageId): int
        {
            return $this->count(['image_id' => $imageId, 'vote' => '1']);
        }

        public function countBadByImageId(int $imageId): int
        {
            return $this->count(['image_id' => $imageId, 'vote' => '-1']);
        }

    }
