<?php
    namespace DatabasesLocation;

    class Ip2location extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'location';
        protected array $fields = ['ip_from', 'ip_to', 'country', 'country_name'];
        protected array $primaryKey = ['ip_from', 'ip_to'];
        protected string $tableName = 'ip2location';

        public function findCountryByIpTo(string $ip): ?array
        {
            return $this->sqlReadRow('SELECT country FROM ip2location WHERE ip_to >= ? ORDER BY ip_to LIMIT 1', [$ip]);
        }

        public function findCountryInfoByIpTo(string $ip): ?array
        {
            return $this->sqlReadRow('SELECT country, country_name FROM ip2location WHERE ip_to >= ? ORDER BY ip_to LIMIT 1', [$ip]);
        }

        public function createReplaceTableLikeMain(): void
        {
            $this->sqlWrite('CREATE TABLE IF NOT EXISTS ip2location_replace LIKE ip2location');
        }

        public function truncateReplaceTable(): void
        {
            $this->sqlWrite('TRUNCATE TABLE ip2location_replace');
        }

        public function insertReplaceRow(array $row): void
        {
            $this->sqlWrite(
                'INSERT INTO ip2location_replace SET ip_from = ?, ip_to = ?, country = ?, country_name = ?',
                [
                    $row[0] ?? '',
                    $row[1] ?? '',
                    $row[2] ?? '',
                    $row[3] ?? ''
                ]
            );
        }

        public function swapReplaceIntoMain(): void
        {
            $this->sqlWrite('DROP TABLE ip2location');
            $this->sqlWrite('RENAME TABLE ip2location_replace TO ip2location');
        }

    }
