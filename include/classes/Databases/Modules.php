<?php
    namespace Databases;

    class Modules extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['name', 'type', 'filename', 'highlight', 'permission', 'membership', 'secure'];
        protected array $primaryKey = ['name', 'type'];
        protected string $tableName = 'modules';

    }
