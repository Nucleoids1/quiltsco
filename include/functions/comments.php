<?php
function comments($link, $table)
{
    if (!in_array($table, ['tracker_bugs_comments', 'tiles_comments', 'quilts_comments', 'images_comments', 'members_comments'])) {
        return;
    }
    $i = 0;
    echo '<div class="header">Member Comments</div>';
    echo '<div class="content">';
    $commentsRows = (new Databases\Comments($table))->findAllByLinkId($link);
    if ($commentsRows) {
        foreach ($commentsRows as $commentsRow) {
            if ($i) {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            }
            $username = getUsername($commentsRow['user_id']);
            echo '<table style="font-size: 1.0rem; width: 100%; border-collapse: collapse; border-spacing: 0;"><tr>';
            echo '<td style="width: 86px; vertical-align: top;"><a href="?s=image&i=' . getMainImageId($commentsRow['user_id']) . '"><img src="?g=thumb&i=' . getMainImageId($commentsRow['user_id']) . '" style="width: 80px; height: 60px; float: left; border: solid 2px black; margin-right: 10px;" alt=""></a></td>';
            echo '<td style="vertical-align: top;">';
            echo '<a href="?s=profile&u=' . safeAttr($username) . '"><b>' . safeAttr($username) . '</b></a> said @ ' . niceDate($commentsRow['posted_on']) . ($GLOBALS['auth']['root'] || $GLOBALS['auth']['id'] == $commentsRow['user_id'] ? ' [ ' . makePostLink('?a=comment_delete&i=' . $commentsRow['id'] . '&link=' . $commentsRow['link_id'] . '&table=' . $table . '&b=' . encodeUrlPath(server('QUERY_STRING')), 'Delete', 'Delete this comment?') . ' ]' : '');
            echo '<div style="margin: 4px 20px 4px 20px;">' . nl2br(safeHtml($commentsRow['comment'])) . '</div>';
            echo '</td></tr></table>';
            $i++;
        }
    }
    if ($GLOBALS['auth']['id']) {
        $commentsRow = (new \Databases\Comments($table))->findLastByLinkIdAndUserId($link, $GLOBALS['auth']['id']);
        if ($commentsRow) {
            if ($i++) {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            }
            echo '<form action="?a=comment_edit&i=' . $commentsRow['id'] . '&b=' . encodeUrlPath(server('QUERY_STRING')) . '" method="post" style="margin: 0px; display: inline;">';
            echo csrfField();
            echo '<input type="hidden" name="link" value="' . safeAttr($link) . '">';
            echo '<input type="hidden" name="table" value="' . safeAttr($table) . '">';
            echo '<textarea name="comment" class="input_text" style="width: 600px; height: 100px; margin: 0px;">' . safeHtml($commentsRow['comment']) . '</textarea>';
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            echo '<input type="submit" value="Modify Last Comment" class="input_submit">';
            echo '</form>';
        }
        if ($i++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo '<form action="?a=comment_add&b=' . encodeUrlPath(server('QUERY_STRING')) . '" method="post" style="margin: 0px; display: inline;">';
        echo csrfField();
        echo '<input type="hidden" name="link" value="' . safeAttr($link) . '">';
        echo '<input type="hidden" name="table" value="' . safeAttr($table) . '">';
        echo '<textarea name="comment" class="input_text" style="width: 600px; height: 100px; margin: 0px;"></textarea>';
        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        echo '<input type="submit" value="Post New Comment" class="input_submit">';
        echo '</form>';
    } elseif (!$commentsRows) {
        echo 'No member comments available...';
    }
    echo '</div>';
}
