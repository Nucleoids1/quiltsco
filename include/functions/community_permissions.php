<?php
function communityPermissions($community, $section = 0, $forum = 0)
{
    $hasRoot = (new \Databases\CommunityPermissions())->hasPermissionByCommunityAndUser($community, $GLOBALS['auth']['id'], 'administrator');
    $communityPermissionsRows = (new \Databases\CommunityPermissions())->selectDistinctPermissions();
    foreach ($communityPermissionsRows as $communityPermissionsRow) {
        $hasPerm = (new \Databases\CommunityPermissions())->hasPermissionByCommunityAndUser($community, $GLOBALS['auth']['id'], $communityPermissionsRow['permission']);
        if ($GLOBALS['auth']['id'] && ($hasRoot || $hasPerm)) {
            $GLOBALS['auth']['community'][$communityPermissionsRow['permission']] = true;
        } else {
            $GLOBALS['auth']['community'][$communityPermissionsRow['permission']] = false;
            if ($section) {
                if ((new \Databases\CommunitySectionsPermissions())->hasPermissionBySectionAndUser($section, $GLOBALS['auth']['id'], $communityPermissionsRow['permission'])) {
                    $GLOBALS['auth']['community'][$communityPermissionsRow['permission']] = true;
                }
            }
            if ($forum) {
                if ((new \Databases\CommunityForumsPermissions())->hasPermissionByForumAndUser($forum, $GLOBALS['auth']['id'], $communityPermissionsRow['permission'])) {
                    $GLOBALS['auth']['community'][$communityPermissionsRow['permission']] = true;
                }
            }
        }
    }
}
