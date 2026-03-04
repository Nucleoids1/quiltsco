<?php
require_once('../include/functions/community_banned.php');
require_once('../include/functions/community_permissions.php');
require_once('../include/functions/community_reply_overall_vote.php');
require_once('../include/functions/community_thread_overall_rating.php');
require_once('../include/functions/coolness.php');
require_once('../include/functions/ip_decode.php');
require_once('../include/functions/pages.php');
require_once('../include/functions/rw_code.php');

$GLOBALS['highlight'] = 'forum';


if (language() == 'french') {
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_THREAD', 'Désolé, ce sujet n\'existe pas.');
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_FORUM', 'Désolé, ce forum n\'existe pas.');
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_SECTION', 'Désolé, cette section n\'existe pas.');
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_COMMUNITY', 'Désolé, cette communauté n\'existe pas.');
    define('SHOW_COMMUNITY_THREAD_ADD_UPDATE', 'Ajouter une mise à jour');
    define('SHOW_COMMUNITY_THREAD_LOADING_QUOTE', 'Chargement de la citation...');
    define('SHOW_COMMUNITY_THREAD_GOOD', 'Bon');
    define('SHOW_COMMUNITY_THREAD_BAD', 'Mauvais');
    define('SHOW_COMMUNITY_THREAD_COMMUNITY', 'Communautés');
    define('SHOW_COMMUNITY_THREAD_MUST_BE_LOGGED_IN_TO_VOTE', 'Vous devez être connecté afin de voter.');
    define('SHOW_COMMUNITY_THREAD_POST_A_REPLY', 'Publier une réponse');
    define('SHOW_COMMUNITY_THREAD_THIS_THREAD_IS_LOCKED', 'Ce sujet est verrouillé. Aucune publication n\'est permise.');
    define('SHOW_COMMUNITY_THREAD_YOU_CAN_NOT_POST_TWICE', 'Vous ne pouvez pas publier deux fois de suite. Essayez plutôt de mettre à jour votre dernière réponse.');
    define('SHOW_COMMUNITY_THREAD_MUST_BE_LOGGED_IN_TO_REPLY', 'Vous devez être connecté pour publier une réponse.');
    define('SHOW_COMMUNITY_THREAD_DELETE_THIS_THREAD', 'Supprimer ce sujet');
    define('SHOW_COMMUNITY_THREAD_UNLOCK_THIS_THREAD', 'Déverrouiller ce sujet');
    define('SHOW_COMMUNITY_THREAD_LOCK_THIS_THREAD', 'Verrouiller ce sujet');
    define('SHOW_COMMUNITY_THREAD_UN_STICKY_THIS_THREAD', 'Retirer ce sujet des épinglés');
    define('SHOW_COMMUNITY_THREAD_STICKY_THIS_THREAD', 'Épingler ce sujet');
    define('SHOW_COMMUNITY_THREAD_MOVE_THIS_MESSAGE', 'Déplacer ce sujet');
    define('SHOW_COMMUNITY_THREAD_MODERATOR_TOOLS', 'Outils de modération');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_REPLIED_ON', 'a répondu le');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_FROM', 'de');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_UPDATE', 'Mise à jour');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_WROTE_ON', 'a écrit sur');
} else {
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_THREAD', 'Sorry, that thread does not exist.');
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_FORUM', 'Sorry, that forum does not exist.');
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_SECTION', 'Sorry, that section does not exist.');
    define('SHOW_COMMUNITY_THREAD_ERROR_NO_COMMUNITY', 'Sorry, that community does not exist.');
    define('SHOW_COMMUNITY_THREAD_ADD_UPDATE', 'Add Update');
    define('SHOW_COMMUNITY_THREAD_LOADING_QUOTE', 'Loading Quote...');
    define('SHOW_COMMUNITY_THREAD_GOOD', 'notice_good');
    define('SHOW_COMMUNITY_THREAD_BAD', 'Bad');
    define('SHOW_COMMUNITY_THREAD_COMMUNITY', 'Communities');
    define('SHOW_COMMUNITY_THREAD_MUST_BE_LOGGED_IN_TO_VOTE', 'You must be logged-in in order to vote.');
    define('SHOW_COMMUNITY_THREAD_POST_A_REPLY', 'Post A Reply');
    define('SHOW_COMMUNITY_THREAD_THIS_THREAD_IS_LOCKED', 'This thread is locked. No posting is allowed.');
    define('SHOW_COMMUNITY_THREAD_YOU_CAN_NOT_POST_TWICE', 'You can not post twice in a row. Try updating your last reply instead.');
    define('SHOW_COMMUNITY_THREAD_MUST_BE_LOGGED_IN_TO_REPLY', 'You must be logged in to post a reply.');
    define('SHOW_COMMUNITY_THREAD_DELETE_THIS_THREAD', 'Delete This Thread');
    define('SHOW_COMMUNITY_THREAD_UNLOCK_THIS_THREAD', 'Unlock This Thread');
    define('SHOW_COMMUNITY_THREAD_LOCK_THIS_THREAD', 'Lock This Thread');
    define('SHOW_COMMUNITY_THREAD_UN_STICKY_THIS_THREAD', 'Un-Sticky This Thread');
    define('SHOW_COMMUNITY_THREAD_STICKY_THIS_THREAD', 'Sticky This Thread');
    define('SHOW_COMMUNITY_THREAD_MOVE_THIS_MESSAGE', 'Move This Message');
    define('SHOW_COMMUNITY_THREAD_MODERATOR_TOOLS', 'Moderator Tools');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_REPLIED_ON', 'replied on');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_FROM', 'from');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_UPDATE', 'Update');
    define('FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_WROTE_ON', 'wrote on');
}

$_id = getInt('i');
$_page = getInt('p', 1);

$communityRow = (new \Databases\CommunityThreads())->findThreadContextForDisplay($_id);
if (!empty($communityRow)) {
    $communityRow = $communityRow;
    communityBanned($communityRow['community_id']);
    communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

    (new \Databases\CommunityThreads())->incrementViews($communityRow['thread_id']);

    $falseReason = '';
    $allowModify = 0;
    $allowModifyTitle = 0;
    $allowReply = $GLOBALS['auth']['id'] && ((!$communityRow['forum_locked'] && !$communityRow['thread_locked']) || $GLOBALS['auth']['community']['thread_lock_post']);
    if ($allowReply) {
        $communityMessagesRow = (new \Databases\CommunityMessages())->findLatestByThreadId($_id);
        if ($communityMessagesRow) {
            $ago = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date('m') - 6, date('d'), date('Y')));
            if ($communityMessagesRow['message_posted_on'] < $ago) {
                $allowReply = false;
                $falseReason = 'This topic is too old to reply to.';
            }
            if ($GLOBALS['auth']['id'] == $communityMessagesRow['message_user_id']) {
                $ago = date('Y-m-d H:i:s', mktime(date("H"), date("i") - 15, date("s"), date('m'), date('d'), date('Y')));
                if ($communityMessagesRow['message_posted_on'] >= $ago) {
                    $allowModify = $communityMessagesRow['message_id'];
                    if ($communityRow['thread_first_message_id'] == $communityMessagesRow['message_id']) {
                        $allowModifyTitle = $communityRow['thread_title'];
                    }
                }
                $allowReply = false;
            }
        }
    }

    $replyCount = (new \Databases\CommunityMessages())->countByThreadId($communityRow['thread_id']);
    $pageCount = ceil($replyCount / COMMUNITY_REPLIES_PER_PAGE);
    if ($pageCount == 0) {
        $pageCount = 1;
    }

    $lastReadMessage = 0;
    $nextUnreadMessage = 0;
    if ($GLOBALS['auth']['id']) {
        $communityThreadsPointersRow = (new \Databases\CommunityThreadsPointers())->findByUserAndThread($GLOBALS['auth']['id'], $communityRow['thread_id']);
        if ($communityThreadsPointersRow) {
            $lastReadMessage = $communityThreadsPointersRow['message_id'];
            $communityMessagesRow = (new \Databases\CommunityMessages())->findFirstByThreadIdAfterMessage($communityRow['thread_id'], $lastReadMessage);
            if ($communityMessagesRow) {
                $nextUnreadMessage = $communityMessagesRow['message_id'];
            } else {
                $nextUnreadMessage = 'none';
            }
        } else {
            $nextUnreadMessage = $communityRow['thread_first_message_id'];
        }
        if (!get('p')) {
            $previousCount = (new \Databases\CommunityMessages())->countByThreadIdUpToMessage($communityRow['thread_id'], $lastReadMessage);
            $_page = ceil(($previousCount + 1) / COMMUNITY_REPLIES_PER_PAGE);
            if ($_page > $pageCount) {
                $_page = $pageCount;
            }
        }
    }

    $pages = '<span class="pages">Page: ' . pages('?s=community_thread&i=' . $communityRow['thread_id'], $_page, $pageCount) . '</span>';
?>

    <script>
        function quoteMessage(id) {
            ajaxPostAppend('index.php?j=community_quote&i=' + id, 'reply_playarea', '', '', 'value');
            var replyArea = document.getElementById('reply_playarea');
            if (replyArea) {
                replyArea.focus();
            }
        }

        function displayUpdateTextbox(id) {
            var p = '';
            p += '<br />';
            p += '<form action="?a=community_reply_update&i=' + id + '&b=<?php echo safeJs(encodeUrlPath(server('QUERY_STRING'))); ?>" ';
            p += 'method="post" class="form">';
            p += '<input type="hidden" name="csrf_token" value="' + csrfToken + '">';
            p += '<label for="update_body_' + id + '" class="hidden">Update</label><textarea name="body" id="update_body_' + id + '" class="input_text" style="height: 120px; width: 90%;"></textarea>';
            p += '<br /><div style="padding: 5px 0px 0px 0px;"></div>';
            p += '<input type="submit" value="<?php echo safeJs(SHOW_COMMUNITY_THREAD_ADD_UPDATE) ?>" class="input_submit">';
            p += '</form>';
            var obj = document.getElementById('message' + id);
            if (!obj) {
                return false;
            }
            obj.innerHTML = p;
            obj.style.display = 'block';
            return false;
        }

        function rep(str, needle, replace) {
            for (var i = 0; i < str.length; i++) {
                if (str.search(needle) != -1) {
                    str = str.replace(needle, replace);
                }
            }
            return str;
        }
    </script>

    <?php
    $newtitle = '';
    if ($communityRow['thread_tile']) {
        $tilesRow = (new \Databases\Tiles())->findById($communityRow['thread_tile']);
        if ($tilesRow) {
            if ($tilesRow['comment']) {
                $newtitle = substr($tilesRow['comment'], 0, 50);
            }
        } else {
            $newtitle = 'Removed Tile';
        }
    }

    include('../include/parts/header.php');

    echo boxOutsideTop('<a href="?s=community">' . SHOW_COMMUNITY_THREAD_COMMUNITY . '</a> - <a href="?s=community&i=' . $communityRow['community_id'] . '">' . safeAttr($communityRow['community_name']) . '</a> - <a href="?s=community_forum&i=' . $communityRow['forum_id'] . '">' . safeAttr($communityRow['forum_name_' . language()]) . '</a> - Reading Thread', '[ <a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=new">New Threads</a> | <a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=my_new">Participating New Threads</a> ]');
    echo boxInsideTop();
    echo '<table role="presentation" style="width: 100%; border-spacing: 0;"><tr>';
    echo '<td>';
    echo '<span style="font-size: 1.05rem; font-weight: bold;">';
    if ($communityRow['thread_locked']) {
        echo '[ <span class="notice_attention">Locked</span> ] ';
    }
    if ($communityRow['thread_sticky']) {
        echo '[ <span class="notice_error">Sticky</span> ] ';
    }
    echo htmlentities($newtitle ? $newtitle : $communityRow['thread_title']);
    echo '</span>';
    echo '</td>';
    echo '<td style="text-align: right;">';
    echo '<span style="font-weight: bold;">Rating: <span id="thread_ranking">' . threadOverallRating($communityRow['thread_id']) . '</span></span>';
    if ($GLOBALS['auth']['id'] && $communityRow['thread_user_id'] != $GLOBALS['auth']['id']) {
        echo '&nbsp;&nbsp;&nbsp;<form class="form" style="display: inline"><span style="font-weight: bold;">Change:</span> <select id="rating" name="rating" class="input_select" onChange="ajaxPost(\'index.php?j=thread_rating&i=' . $communityRow['thread_id'] . '&v=\' + document.getElementById(\'rating\').value, \'thread_ranking\'); return false;"><option value="0"></option>';
        $currentVote = 0;
        $currentVoteRow = (new \Databases\CommunityThreadsRatings())->findByThreadAndUser($_id, $GLOBALS['auth']['id']);
        if ($currentVoteRow) {
            $currentVote = $currentVoteRow['category_id'];
        }
        $communityThreadsCategoriesRows = (new \Databases\CommunityThreadsCategories())->selectAllByName();
        foreach ($communityThreadsCategoriesRows as $communityThreadsCategoriesRow) {
            echo '<option value="' . $communityThreadsCategoriesRow['id'] . '" class="' . ($communityThreadsCategoriesRow['positive'] ? 'notice_good' : 'notice_error') . '"' . ($currentVote == $communityThreadsCategoriesRow['id'] ? ' selected' : '') . '>' . $communityThreadsCategoriesRow['name'] . '</option>';
        }
        echo '</select></form>';
    }
    echo '</td>';
    echo '</tr></table>';
    echo boxInsideBottom();
    echo boxOutsideBottom();

    echo boxOutsideTop('<a href="?s=community">' . SHOW_COMMUNITY_THREAD_COMMUNITY . '</a> - <a href="?s=community&i=' . $communityRow['community_id'] . '">' . safeAttr($communityRow['community_name']) . '</a> - <a href="?s=community_forum&i=' . $communityRow['forum_id'] . '">' . safeAttr($communityRow['forum_name_' . language()]) . '</a> - Reading Thread<br />' . $pages, '[ <a href="?s=community_help">Help</a> ]');
    $i = 0;
    $lastDisplayedMessage = 0;
    $communityMessagesRows = (new \Databases\CommunityMessages())->selectPageByThreadId($communityRow['thread_id'], ($_page - 1) * COMMUNITY_REPLIES_PER_PAGE, COMMUNITY_REPLIES_PER_PAGE);
    if (count($communityMessagesRows)) {
        foreach ($communityMessagesRows as $communityMessagesRow) {
            if ($GLOBALS['auth']['id']) {
                $lastDisplayedMessage = $communityMessagesRow['message_id'];
            }
            if ($nextUnreadMessage == $communityMessagesRow['message_id']) {
                if ($i++) {
                    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                }
                echo '<div id="l" class="inside" style="font-size: 1.0rem; font-weight: bold; padding-left: 8px;"><span class="notice_good">You\'ve read up to this point.</span></div>';
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            } elseif ($i++) {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            }
            $isIgnored = (new \Databases\Ignored())->isIgnoredByUser($GLOBALS['auth']['id'], $communityMessagesRow['message_user_id']);
            if ($isIgnored) {
                $messageThreashold = 10000;
            } else {
                $messageThreashold = -5;
            }
            $allowUpdate = ($communityMessagesRow['message_user_id'] == $GLOBALS['auth']['id'] && !$communityRow['thread_locked']) || $GLOBALS['auth']['community']['thread_lock_post'] ? true : false;
            $rating = replyOverallRating($communityMessagesRow['message_id']);
            if ($rating == 0) {
                $ratingStr = 'Neutral [0]';
            } elseif ($rating > 0) {
                $ratingStr = '<span class="notice_good">Good [+' . $rating . ']</span>';
            } else {
                $ratingStr = '<span class="notice_error">Bad [' . $rating . ']</span>';
            }
            echo '<table role="presentation" style="width: 100%; border-spacing: 0;">';
            echo '<tr><td class="inside" style="font-size: 1.1rem; padding: 5px;">';
            echo '<span id="r' . $communityMessagesRow['message_id'] . '" style="float: right; font-weight: bold;">' . $ratingStr;
            if ($GLOBALS['auth']['id'] && $GLOBALS['auth']['id'] != $communityMessagesRow['message_user_id'] && !(new \Databases\CommunityMessagesRating())->hasVoteByMessageAndUser($communityMessagesRow['message_id'], $GLOBALS['auth']['id'])) {
                echo '&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><span class="notice_good"><button type="button" onClick="ajaxJson(\'index.php?j=reply_vote&i=' . safeJs($communityMessagesRow['message_id']) . '&v=good\');" style="background: none; border: 0; padding: 0; color: inherit; cursor: pointer;">Good</button></span> / <span class="notice_error"><button type="button" onClick="ajaxJson(\'index.php?j=reply_vote&i=' . safeJs($communityMessagesRow['message_id']) . '&v=bad\');" style="background: none; border: 0; padding: 0; color: inherit; cursor: pointer;">Bad</button></span></span>';
            }
            echo '</span>';
            if ($GLOBALS['auth']['community']['thread_delete'] || $GLOBALS['auth']['community']['message_delete']) {
                if ($communityMessagesRow['message_id'] == $communityRow['thread_first_message_id']) {
                    if ($GLOBALS['auth']['community']['thread_delete']) {
                        echo '<span style="float: right; margin-right: 15px;">' . makePostLink('?a=community_thread_delete&i=' . $communityRow['thread_id'], 'Delete Thread', 'Delete this thread?') . '</span>';
                    }
                } else {
                    if ($GLOBALS['auth']['community']['message_delete']) {
                        echo '<span style="float: right; margin-right: 15px;">' . makePostLink('?a=community_reply_delete&i=' . $communityMessagesRow['message_id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')), 'Delete Reply', 'Delete this reply?') . '</span>';
                    }
                }
            }
            echo '<span style="float: right; margin-right: 15px;">' . makeLink('javascript:toggle(\'i' . safeJs($communityMessagesRow['message_id']) . '\');', 'Toggle Reply') . '</span>';
            echo '<b>' . getUsername($communityMessagesRow['message_user_id'], 1) . '</b> ' . FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_REPLIED_ON . ' ' . niceDate($communityMessagesRow['message_posted_on']);
            echo '</td></tr>';
            echo '</table>';
            echo '<div id="i' . $communityMessagesRow['message_id'] . '" style="display: ' . ($rating > $messageThreashold ? 'block' : 'none') . ';">';
            echo '<table role="presentation" style="width: 100%; border-spacing: 0;">';
            echo '<tr><td colspan="2" style="height: 4px; white-space: nowrap;"></td></tr>';
            echo '<tr>';
            echo '<td rowspan="3" style="width: 160px; font-weight: bold; vertical-align: top; text-align: left;">';
            echo boxImageTop('margin: 0px 5px 0px 0px;');
            echo '<a href="?s=member&u=' . getUsername($communityMessagesRow['message_user_id']) . '"><img src="?g=thumb&i=' . getMainImageId($communityMessagesRow['message_user_id']) . '" alt="' . getUsername($communityMessagesRow['message_user_id']) . '" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: 0px;" /></a>';
            echo '<div style="padding: 3px 0px 0px 0px;"></div>';
            echo 'Coolness: ' . coolness($communityMessagesRow['message_user_id']) . '<br />';
            echo boxImageBottom();
            echo '</td>';
            echo '<td class="inside" style="height: 90px; vertical-align: top;">';
            echo '<div style="font-size: 1.0rem;">';
            $communityMessagesBodiesRow = (new \Databases\CommunityMessagesBodies())->findById($communityMessagesRow['message_id']);
            if ($communityMessagesBodiesRow) {
                echo transformBody($communityMessagesBodiesRow['message_body']);
            } else {
                echo 'No Body!';
            }
            echo '</div>';
            $communityMessagesUpdatesRows = (new \Databases\CommunityMessagesUpdates())->selectByMessageId($communityMessagesRow['message_id']);
            if (count($communityMessagesUpdatesRows)) {
                echo '<br />';
                foreach ($communityMessagesUpdatesRows as $communityMessagesUpdatesRow) {
                    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                    echo '<span class="notice_attention"><b>' . FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_UPDATE . '</b></span> ';
                    echo '<b>' . getUsername($communityMessagesUpdatesRow['update_user_id'], 1) . '</b> ' . FUNCTIONS_COMMUNITY_DISPLAY_MESSAGE_WROTE_ON . ' ' . niceDate($communityMessagesUpdatesRow['update_posted_on']);
                    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                    echo '<div style="font-size: 1.0rem;">';
                    echo transformBody($communityMessagesUpdatesRow['update_body']);
                    echo '</div>';
                }
            }
            echo '<div id="message' . $communityMessagesRow['message_id'] . '" style="display: none;"></div>';
            echo '</td>';
            echo '</tr>';
            echo '<tr><td style="height: 4px; white-space: nowrap;"></td></tr>';
            echo '<tr><td class="inside" style="height: 18px; padding: 5px;">';
            if ($allowReply) {
                echo '<span style="float: right; margin-left: 15px;">' . makeLink('javascript:quoteMessage(' . safeJs($communityMessagesRow['message_id']) . ');', 'Quote Message') . '</span>';
            }
            if ($allowUpdate) {
                echo '<span style="float: right;"><button type="button" onClick="return displayUpdateTextbox(' . safeJs($communityMessagesRow['message_id']) . ');" style="background: none; border: 0; padding: 0; color: #00f; text-decoration: underline; cursor: pointer;">Add Update</button></span>';
            }
            if ($communityMessagesRow['message_mood']) {
                $membersMoodsRow = (new \Databases\MembersMoods())->findById($communityMessagesRow['message_mood']);
                if ($membersMoodsRow) {
                    echo 'I\'m feeling ' . $membersMoodsRow['mood'] . ' right now..';
                }
            }
            echo '</td></tr>';
            echo '</table>';
            echo '</div>';
            if ($communityRow['thread_last_message_id'] == $communityMessagesRow['message_id'] && $nextUnreadMessage == 'none') {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo '<div id="l" class="inside" style="font-size: 1.0rem; font-weight: bold; padding-left: 8px;"><span class="notice_good">You\'ve already read all the messages in this thread.</span></div>';
            }
        }
    } else {
        echo '<table role="presentation" style="width: 100%; border-spacing: 0;">';
        echo '<tr><td class="inside" style="padding: 5px;">';
        echo 'There are no messages in this thread yet.';
        echo '</td></tr>';
        echo '</table>';
    }

    echo boxOutsideBottom();

    if (count($communityMessagesRows) > 5) {
        echo makePages('<a href="?s=community">' . SHOW_COMMUNITY_THREAD_COMMUNITY . '</a> - <a href="?s=community&i=' . $communityRow['community_id'] . '">' . safeAttr($communityRow['community_name']) . '</a> - <a href="?s=community_forum&i=' . $communityRow['forum_id'] . '">' . safeAttr($communityRow['forum_name_' . language()]) . '</a> - Reading Thread<br />' . $pages);
    }

    if ($GLOBALS['auth']['id'] && $lastDisplayedMessage > $lastReadMessage) {
        (new \Databases\CommunityThreadsPointers())->updateReadPointer($communityRow['thread_id'], $GLOBALS['auth']['id'], $lastDisplayedMessage);
    }

    echo boxOutsideTop(SHOW_COMMUNITY_THREAD_POST_A_REPLY, '[ <a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=new">New Threads</a> | <a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=my_new">Participating New Threads</a> ]');
    echo boxInsideTop();
    if ($allowReply) {
    ?>

        <form action="?a=community_reply_add&i=<?php echo $communityRow['thread_id']; ?>&b=<?php echo encodeUrlPath(server('QUERY_STRING')) ?>" method="post" class="form">
            <?php echo csrfField(); ?>
            <label for="reply_playarea" class="hidden"><?php echo SHOW_COMMUNITY_THREAD_POST_A_REPLY ?></label>
            <textarea name="body" id="reply_playarea" class="input_text" style="height: 120px; width: 90%"></textarea>
            <div style="padding: 5px 0px 0px 0px;"></div>
            <input type="submit" value="<?php echo SHOW_COMMUNITY_THREAD_POST_A_REPLY ?>" class="input_submit">
        </form>

<?php
    } elseif ($GLOBALS['auth']['id'] && (($communityRow['forum_locked'] || $communityRow['thread_locked']) && !$GLOBALS['auth']['community']['thread_lock_post'])) {
        echo SHOW_COMMUNITY_THREAD_THIS_THREAD_IS_LOCKED;
    } elseif ($falseReason) {
        echo $falseReason;
    } elseif ($GLOBALS['auth']['id']) {
        if ($allowModify) {
            $communityMessagesBodiesRow = (new \Databases\CommunityMessagesBodies())->findById($allowModify);
            if ($communityMessagesBodiesRow) {
                echo 'You can edit your message for up to 15 minutes after you have posted it.';
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo '<form action="?a=community_reply_modify&i=' . $allowModify . '&b=' . encodeUrlPath(server('QUERY_STRING')) . '" method="post" class="form">';
                echo csrfField();
                if ($allowModifyTitle) {
                    echo '<label for="modify_title" class="hidden">Title</label><input name="title" id="modify_title" class="input_text" style="width: 90%" value="' . safeAttr($allowModifyTitle) . '">';
                    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                }
                echo '<label for="modify_body" class="hidden">Message Body</label><textarea name="body" id="modify_body" class="input_text" style="height: 120px; width: 90%">' . safeAttr($communityMessagesBodiesRow['message_body']) . '</textarea>';
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo '<input type="submit" value="Modify Message" class="input_submit">';
                echo '</form>';
            } else {
                echo 'Message Not Found...';
            }
        } else {
            echo SHOW_COMMUNITY_THREAD_YOU_CAN_NOT_POST_TWICE;
        }
    } else {
        echo SHOW_COMMUNITY_THREAD_MUST_BE_LOGGED_IN_TO_REPLY;
    }
    echo boxInsideBottom();
    echo boxOutsideBottom();

    if ($GLOBALS['auth']['community']['thread_delete'] || $GLOBALS['auth']['community']['thread_lock'] || $GLOBALS['auth']['community']['thread_sticky'] || $GLOBALS['auth']['community']['thread_move']) {
        $i = 0;
        echo boxOutsideTop(SHOW_COMMUNITY_THREAD_MODERATOR_TOOLS);
        echo boxInsideTop();
        if ($GLOBALS['auth']['community']['thread_delete']) {
            echo makePostLink('?a=community_thread_delete&i=' . $communityRow['thread_id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')), SHOW_COMMUNITY_THREAD_DELETE_THIS_THREAD, 'Are you sure you want to delete this message?\r\n You cannot undo this.') . '<br />';
        }
        if ($GLOBALS['auth']['community']['thread_lock']) {
            if ($communityRow['thread_locked']) {
                echo makePostLink('?a=community_thread_lock&i=' . $communityRow['thread_id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')) . '&unlock=1', SHOW_COMMUNITY_THREAD_UNLOCK_THIS_THREAD, 'Are you sure you want to unlock this message?\r\n Any member will be able to post again.') . '<br />';
            } else {
                echo makePostLink('?a=community_thread_lock&i=' . $communityRow['thread_id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')) . '&lock=1', SHOW_COMMUNITY_THREAD_LOCK_THIS_THREAD, 'Are you sure you want to lock this message?\r\n No further posting will be allowed (except by authorised moderators).') . '<br />';
            }
        }
        if ($GLOBALS['auth']['community']['thread_sticky']) {
            if ($communityRow['thread_sticky']) {
                echo makePostLink('?a=community_thread_sticky&i=' . $communityRow['thread_id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')) . '&unstick=1', SHOW_COMMUNITY_THREAD_UN_STICKY_THIS_THREAD, 'Are you sure you want to make this a regular message?\r\n It wont appear at the top of the forum anymore.') . '<br />';
            } else {
                echo makePostLink('?a=community_thread_sticky&i=' . $communityRow['thread_id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')) . '&stick=1', SHOW_COMMUNITY_THREAD_STICKY_THIS_THREAD, 'Are you sure you want to sticky this message?\r\n It will appear at the top of the forum.') . '<br />';
            }
        }
        if ($GLOBALS['auth']['community']['thread_move']) {
            echo makeLink('?s=community_thread_move&i=' . $communityRow['thread_id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')), SHOW_COMMUNITY_THREAD_MOVE_THIS_MESSAGE) . '<br />';
        }
        echo boxInsideBottom();
        echo boxOutsideBottom();
    }

    if ($GLOBALS['auth']['id']) {
        echo makePages('<a href="?s=community">' . SHOW_COMMUNITY_THREAD_COMMUNITY . '</a> - <a href="?s=community&i=' . $communityRow['community_id'] . '">' . safeAttr($communityRow['community_name']) . '</a> - <a href="?s=community_forum&i=' . $communityRow['forum_id'] . '">' . safeAttr($communityRow['forum_name_' . language()]) . '</a> - Reading Thread');
    }

    include('../include/parts/footer.php');
} else {
    header('Location: ./?s=communities');
    die;
}

function transformBody($b)
{
    return bbcodes(imagesCode(nl2br(smileys(safeAttr($b)))));
}
