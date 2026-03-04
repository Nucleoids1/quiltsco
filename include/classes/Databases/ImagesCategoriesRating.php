<?php
    namespace Databases;

    class ImagesCategoriesRating extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'image_id', 'category_id', 'posted_on'];
        protected array $primaryKey = ['user_id', 'image_id'];
        protected string $tableName = 'images_categories_rating';

        public function upsertRating(int $imageId, int $categoryId, int $userId): void
        {
            $this->replaceArray([
                'image_id' => $imageId,
                'category_id' => $categoryId,
                'user_id' => $userId,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ]);
        }

        public function countByImageId(int $imageId): int
        {
            return $this->count(['image_id' => $imageId]);
        }

        public function selectTopCategoriesByImageId(int $imageId, int $limit): array
        {
            return $this->sqlRead(
                'SELECT COUNT(*) AS num, category_id FROM images_categories_rating WHERE image_id = ? GROUP BY category_id ORDER BY num DESC' . $this->buildLimitOffsetClause($limit, 0),
                [$imageId]
            );
        }

        public function selectTopCategoryByImageId(int $imageId): ?array
        {
            return $this->sqlReadRow(
                'SELECT COUNT(*) AS num, category_id FROM images_categories_rating WHERE image_id = ? GROUP BY category_id ORDER BY num DESC LIMIT 1',
                [$imageId]
            );
        }

        public function deleteByImageIdAndUserId(int $imageId, int $userId): void
        {
            $this->deleteWhere(['image_id' => $imageId, 'user_id' => $userId]);
        }

        public function findByImageIdAndUserId(int $imageId, int $userId): ?array
        {
            return $this->selectWhereRow(['image_id' => $imageId, 'user_id' => $userId]);
        }

    }
