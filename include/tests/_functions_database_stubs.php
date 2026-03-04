<?php

declare(strict_types=1);

namespace Databases {
    class CommunityPermissions
    {
        public static int $hasRoot = 0;
        public static array $userPerms = [];

        public function hasPermissionByCommunityAndUser(int $communityId, int $userId, string $permission): bool
        {
            if ($permission === 'administrator') {
                return self::$hasRoot === 1;
            }

            return in_array($permission, self::$userPerms, true);
        }

        public function selectDistinctPermissions(): array
        {
            $all = array_unique(array_merge(['administrator'], self::$userPerms, CommunitySectionsPermissions::$perms, CommunityForumsPermissions::$perms));
            return array_map(static fn (string $permission): array => ['permission' => $permission], array_values($all));
        }
    }

    class CommunitySectionsPermissions
    {
        public static array $perms = [];

        public function hasPermissionBySectionAndUser(int $sectionId, int $userId, string $permission): bool
        {
            return in_array($permission, self::$perms, true);
        }
    }

    class CommunityForumsPermissions
    {
        public static array $perms = [];

        public function hasPermissionByForumAndUser(int $forumId, int $userId, string $permission): bool
        {
            return in_array($permission, self::$perms, true);
        }
    }

    class CommunityMessagesRating
    {
        public static array $counts = ['1' => 0, '-1' => 0];

        public function countByMessageAndVote(int $messageId, string $vote): int
        {
            return (int) (self::$counts[$vote] ?? 0);
        }
    }

    class CommunityThreadsRatings
    {
        public static int $count = 0;
        public static array $positiveRows = [];
        public static array $groupedRow = ['category_id' => 0];

        public function countByThreadId(int $threadId): int
        {
            return self::$count;
        }

        public function countPositiveByThreadId(int $threadId): int
        {
            return count(self::$positiveRows);
        }

        public function findTopCategoryStatsByThreadId(int $threadId): array
        {
            return self::$groupedRow;
        }
    }

    class CommunityThreadsCategories
    {
        public static array $categories = [];

        public function findById(int $categoryId): array
        {
            return self::$categories[$categoryId] ?? ['name' => '', 'positive' => 1];
        }
    }

    class ImagesRating
    {
        public static array $counts = ['1' => 0, '-1' => 0];

        public function countGoodByImageId(int $imageId): int
        {
            return (int) (self::$counts['1'] ?? 0);
        }

        public function countBadByImageId(int $imageId): int
        {
            return (int) (self::$counts['-1'] ?? 0);
        }
    }

    class ImagesCategoriesRating
    {
        public static int $voteCount = 0;
        public static array $topTwo = [];
        public static array $topOne = ['category_id' => 0];

        public function countByImageId(int $imageId): int
        {
            return self::$voteCount;
        }

        public function selectTopCategoriesByImageId(int $imageId, int $limit): array
        {
            return self::$topTwo;
        }

        public function selectTopCategoryByImageId(int $imageId): array
        {
            return self::$topOne;
        }
    }

    class ImagesCategories
    {
        public static array $map = [];

        public function findById(int $categoryId): array
        {
            return self::$map[$categoryId] ?? ['name' => '', 'worksafe' => 1];
        }
    }

    class MembersCreate
    {
        public static int $emailCount = 0;
        public static int $ipCount = 0;
        public static array $deletedWhere = [];

        public function deleteOldPendingAccounts(): void
        {
            self::$deletedWhere = ['posted_on <' => date('Y-m-d H:i:s', strtotime('-10 minutes'))];
        }

        public function countRecentByEmail(string $email, string $since): int
        {
            return self::$emailCount;
        }

        public function countRecentByIp(string $ip, string $since): int
        {
            return self::$ipCount;
        }
    }
}

namespace DatabasesLocation {
    class Ip2location
    {
        public static ?array $row = null;

        public function findCountryByIpTo(int|string $ip): ?array
        {
            return self::$row;
        }

        public function findCountryInfoByIpTo(int|string $ip): ?array
        {
            return self::$row;
        }
    }
}
