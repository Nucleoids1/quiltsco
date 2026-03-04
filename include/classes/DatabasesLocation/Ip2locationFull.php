<?php
    namespace DatabasesLocation;

    class Ip2locationFull extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'location';
        protected array $fields = ['ip_from', 'ip_to', 'country', 'country_name', 'region', 'city', 'latitude', 'longitude', 'zip', 'timezone'];
        protected array $primaryKey = ['ip_from', 'ip_to'];
        protected string $tableName = 'ip2location_full';

        public function createReplaceTableLikeMain(): void
        {
            $this->sqlWrite('CREATE TABLE IF NOT EXISTS ip2location_full_replace LIKE ip2location_full');
        }

        public function truncateReplaceTable(): void
        {
            $this->sqlWrite('TRUNCATE TABLE ip2location_full_replace');
        }

        public function insertReplaceRow(array $row): void
        {
            $this->sqlWrite(
                'INSERT INTO ip2location_full_replace SET ip_from = ?, ip_to = ?, country = ?, country_name = ?, region = ?, city = ?, latitude = ?, longitude = ?, zip = ?, timezone = ?',
                [
                    $row[0] ?? '',
                    $row[1] ?? '',
                    $row[2] ?? '',
                    $row[3] ?? '',
                    $row[4] ?? '',
                    $row[5] ?? '',
                    $row[6] ?? '',
                    $row[7] ?? '',
                    $row[8] ?? '',
                    $row[9] ?? ''
                ]
            );
        }

        public function swapReplaceIntoMain(): void
        {
            $this->sqlWrite('DROP TABLE ip2location_full');
            $this->sqlWrite('RENAME TABLE ip2location_full_replace TO ip2location_full');
        }

    }
