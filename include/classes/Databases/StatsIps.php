<?php
    namespace Databases;

    class StatsIps extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['ip', 'user_id', 'country', 'used_on'];
        protected array $primaryKey = ['ip', 'user_id'];
        protected string $tableName = 'stats_ips';



        public function selectByUserOrderedIp(int $userId): array
        {
            return $this->selectWhere([
                'user_id' => $userId,
            ], 'ip');
        }

        public function selectByIpWithKnownUsers(string $ip): array
        {
            return $this->selectWhere([
                'ip' => $ip,
                'user_id >' => 0,
            ]);
        }

    }
