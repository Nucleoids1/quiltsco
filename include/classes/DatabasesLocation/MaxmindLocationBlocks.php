<?php
    namespace DatabasesLocation;

    class MaxmindLocationBlocks extends \DatabasesSchemes\General {

        protected string $database = 'location';
        protected array $fields = ['ip14', 'ip_from', 'ip_to', 'locId'];
        protected string $tableName = 'maxmind_location_blocks';

    }
