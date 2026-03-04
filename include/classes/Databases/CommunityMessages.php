<?php
    namespace Databases;

    class CommunityMessages extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['message_id', 'thread_id', 'message_user_id', 'message_posted_on', 'message_ip', 'message_mood'];
        protected string $primaryKey = 'message_id';
        protected string $tableName = 'community_messages';

        public function findLegacyMinReplyIdByThreadId(int $threadId): ?array
        {
            return $this->sqlReadRow('SELECT MIN(id) AS min FROM community_replies WHERE thread_id = ?', [$threadId]);
        }

        public function findLegacyMaxReplyIdByThreadId(int $threadId): ?array
        {
            return $this->sqlReadRow('SELECT MAX(id) AS max FROM community_replies WHERE thread_id = ?', [$threadId]);
        }

        public function findLegacyMaxForumReplyIdByForumId(int $forumId): ?array
        {
            return $this->sqlReadRow('SELECT MAX(id) AS max FROM forum_replies WHERE forum_id = ?', [$forumId]);
        }

        public function findLatestByThreadId(int $threadId): ?array
        {
            return $this->selectWhereRow(['thread_id' => $threadId], 'message_id DESC');
        }

        public function countByLegacyForumId(int $forumId): int
        {
            return $this->count(['forum_id' => $forumId]);
        }

        public function countByUserAndThreadId(int $userId, int $threadId): int
        {
            return $this->count([
                'message_user_id' => $userId,
                'thread_id' => $threadId,
            ]);
        }

        public function countByThreadIdAfterMessage(int $threadId, int $messageId): int
        {
            return $this->count([
                'thread_id' => $threadId,
                'message_id >' => $messageId,
            ]);
        }


        public function countByThreadIdBetweenMessages(int $threadId, int $afterMessageId, int $upToMessageId): int
        {
            return $this->count([
                'thread_id' => $threadId,
                'message_id >' => $afterMessageId,
                'message_id <=' => $upToMessageId,
            ]);
        }


        public function countByThreadIdBeforeMessage(int $threadId, int $messageId): int
        {
            return $this->count([
                'thread_id' => $threadId,
                'message_id <' => $messageId,
            ]);
        }

        public function countByThreadIdUpToMessage(int $threadId, int $messageId): int
        {
            return $this->count([
                'thread_id' => $threadId,
                'message_id <=' => $messageId,
            ]);
        }

        public function findFirstByThreadIdAfterMessage(int $threadId, int $messageId): ?array
        {
            return $this->selectWhereRow([
                'thread_id' => $threadId,
                'message_id >' => $messageId,
            ], 'message_id');
        }

        public function selectPageByThreadId(int $threadId, int $offset, int $limit): array
        {
            return $this->selectWhere([
                'thread_id' => $threadId,
            ], 'message_id', $limit, [
                'offset' => $offset,
            ]);
        }

        public function countByThreadId(int $threadId): int
        {
            return $this->count(['thread_id' => $threadId]);
        }

        public function findMessageContext(int $messageId): ?array
        {
            return $this->sqlReadRow(
                'SELECT * FROM community_messages, community_threads, community_forums, community_sections, community WHERE community_messages.message_id = ? AND community_messages.thread_id = community_threads.thread_id AND community_threads.forum_id = community_forums.forum_id AND community_forums.section_id = community_sections.section_id AND community_sections.community_id = community.community_id',
                [$messageId]
            );
        }

        public function addReply(int $threadId, int $userId, string $ip, int $mood): int
        {
            return $this->insertArray([
                'thread_id' => $threadId,
                'message_user_id' => $userId,
                'message_posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'message_ip' => $ip,
                'message_mood' => $mood
            ]);
        }

        public function assignThread(int $messageId, int $threadId): void
        {
            $this->updateWhere(['thread_id' => $threadId], ['message_id' => $messageId]);
        }

        public function deleteByThreadId(int $threadId): void
        {
            $this->deleteWhere(['thread_id' => $threadId]);
        }

        public function findWithBodyById(int $messageId): ?array
        {
            return $this->sqlReadRow(
                "SELECT community_messages.message_user_id, community_messages_bodies.message_body FROM community_messages, community_messages_bodies WHERE community_messages.message_id = ? AND community_messages.message_id = community_messages_bodies.message_id",
                [$messageId]
            );
        }

        public function findById(int $messageId): ?array
        {
            return $this->selectPrimaryKey($messageId);
        }

        public function deleteById(int $messageId): void
        {
            $this->deletePrimaryKey($messageId);
        }

        public function countByUserId(int $userId): int
        {
            return $this->count(['message_user_id' => $userId]);
        }

    }
