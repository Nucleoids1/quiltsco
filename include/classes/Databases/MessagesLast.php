<?php
    namespace Databases;

    class MessagesLast extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['user_id', 'message_id'];
        protected string $primaryKey = 'user_id';
        protected string $tableName = 'messages_last';

    }
