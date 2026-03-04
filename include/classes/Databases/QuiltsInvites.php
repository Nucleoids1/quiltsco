<?php
    namespace Databases;

    class QuiltsInvites extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'quilt_id', 'active'];
        protected array $primaryKey = ['user_id', 'quilt_id'];
        protected string $tableName = 'quilts_invites';


        public function selectWithMembersByQuilt(int $quiltId): array
        {
            return $this->sqlRead(
                'SELECT * FROM quilts_invites INNER JOIN members ON quilts_invites.user_id = members.id WHERE quilts_invites.quilt_id = ? ORDER BY members.username',
                [$quiltId]
            );
        }

    }
