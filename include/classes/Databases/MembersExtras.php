<?php
    namespace Databases;

    class MembersExtras extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['user_id', 'language', 'fullname', 'gender', 'seeking', 'birthday', 'birthday_year', 'birthday_month_day', 'country', 'region', 'city', 'latitude', 'longitude', 'profile_headline', 'profile', 'website', 'aim', 'icq', 'msn', 'gtalk', 'yahoo', 'privacy', 'notification', 'display_email', 'views', 'views_ip'];
        protected string $primaryKey = 'user_id';
        protected string $tableName = 'members_extras';

        public function createExtras(int $userId): void
        {
            $this->insertArray([
                'user_id' => $userId,
            ]);
        }

        public function findByUserId(int $userId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId]);
        }

        public function findWithNotificationByUserId(int $userId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'notification' => 1]);
        }

        public function upsertProfile(int $userId, string $fullname, string $birthday, int $gender, string $country, string $region, string $city, float $latitude, float $longitude, string $website, string $aim, string $icq, string $msn, string $yahoo, string $gtalk, int $privacy, int $notification): void
        {
            $this->replaceArray([
                'user_id' => $userId,
                'fullname' => $fullname,
                'birthday' => $birthday,
                'gender' => $gender,
                'country' => $country,
                'region' => $region,
                'city' => $city,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'website' => $website,
                'aim' => $aim,
                'icq' => $icq,
                'msn' => $msn,
                'yahoo' => $yahoo,
                'gtalk' => $gtalk,
                'privacy' => $privacy,
                'notification' => $notification,
            ]);
        }

    }
