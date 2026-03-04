<?php
$GLOBALS['highlight'] = 'quilts_moderate';

include('../include/parts/header.php');

echo '<h1 class="header">';
echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="vertical-align: top;">';
echo 'Moderate Quilts';
echo '</td>';
if ($GLOBALS['auth']['create_quilt']) {
    echo '<td style="color: #cccccc; font-weight: bold; text-align: right; vertical-align: top;">[ ' . makeLink('?s=quilt_create', 'Create A New Quilt') . ' ]</td>';
}
echo '</tr></table>';
echo '</h1>';

echo '<div class="content">';
$statusFilter = strtolower(trim(get('status', 'all')));
if (!in_array($statusFilter, ['all', 'active', 'inactive'], true)) {
    $statusFilter = 'all';
}

$statusFilterLabel = ucfirst($statusFilter);
echo '<div style="margin-bottom: 12px; text-align: right;">';
echo '<label for="quilts_moderate_status" style="margin-right: 6px; color: #b7c3ca;">Status:</label>';
echo '<select class="input_select" id="quilts_moderate_status" onchange="window.location=this.value;">';
echo '<option value="?s=quilts_moderate"' . ($statusFilter == 'all' ? ' selected' : '') . '>All</option>';
echo '<option value="?s=quilts_moderate&status=active"' . ($statusFilter == 'active' ? ' selected' : '') . '>Active</option>';
echo '<option value="?s=quilts_moderate&status=inactive"' . ($statusFilter == 'inactive' ? ' selected' : '') . '>Inactive</option>';
echo '</select>';
echo '</div>';

$quiltsPermissionsRows = (new \Databases\QuiltsPermissions())->selectByUserOrderedByQuiltDesc($GLOBALS['auth']['id']);
if ($quiltsPermissionsRows) {
    $filteredQuiltsPermissionsRows = [];
    foreach ($quiltsPermissionsRows as $quiltsPermissionsRow) {
        if ($statusFilter == 'active' && !$quiltsPermissionsRow['active']) {
            continue;
        }
        if ($statusFilter == 'inactive' && $quiltsPermissionsRow['active']) {
            continue;
        }
        $filteredQuiltsPermissionsRows[] = $quiltsPermissionsRow;
    }

    echo '<style>';
    echo '.quilts-moderate{display:block;}';
    echo '.quilts-moderate-table{width:100%;border-collapse:separate;border-spacing:0;background:#202529;border:1px solid #3a4247;border-radius:10px;overflow:hidden;}';
    echo '.quilts-moderate-table th{background:#262d31;color:#c7ff70;font-size: 1.0rem;letter-spacing:.04em;text-transform:uppercase;padding:9px 10px;text-align:left;border-bottom:1px solid #3a4247;}';
    echo '.quilts-moderate-table td{padding:10px;border-bottom:1px solid #30383e;color:#d5dee3;vertical-align:middle;}';
    echo '.quilts-moderate-table tr:last-child td{border-bottom:none;}';
    echo '.quilts-moderate-table tr:nth-child(even) td{background:rgba(255,255,255,.01);}';
    echo '.quilts-moderate-meta{color:#94a5ae;font-size: 1.0rem;}';
    echo '.quilts-moderate-actions{text-align:right;white-space:nowrap;}';
    echo '.quilts-moderate-actions .chip{display:inline-block;margin:0 0 4px 6px;padding:3px 7px;border-radius:999px;border:1px solid #3f4b52;background:#22292e;color:#cfdae1;font-size: 1.0rem;line-height:1.4;}';
    echo '.quilts-moderate-status-active{color:#8de89f;font-weight:bold;}';
    echo '.quilts-moderate-status-inactive{color:#ffb2a8;font-weight:bold;}';
    echo '.quilts-moderate-empty{padding:16px 14px;background:#22282d;border:1px dashed #43515a;border-radius:10px;color:#adc0ca;}';
    echo '</style>';


    if ($filteredQuiltsPermissionsRows) {
        echo '<div class="quilts-moderate">';
        echo '<table class="quilts-moderate-table" role="presentation">';
        echo '<tr>';
        echo '<th>Quilt</th>';
        echo '<th>Quilt Size</th>';
        echo '<th>Tile Size</th>';
        echo '<th>Posted</th>';
        echo '<th>Updated</th>';
        echo '<th style="text-align:right;">Moderation</th>';
        echo '</tr>';
        foreach ($filteredQuiltsPermissionsRows as $quiltsPermissionsRow) {
            $quiltsRow = (new \Databases\Quilts())->findById($quiltsPermissionsRow['quilt_id']);
            if ($quiltsRow) {
                echo '<tr>';
                echo '<td style="width: 28%; min-width: 220px;">' . makeLink('?s=quilt&i=' . $quiltsRow['id'], $quiltsRow['name']) . '</td>';
                echo '<td style="width: 10%;"><span class="quilts-moderate-meta">' . $quiltsRow['quilt_width'] . ' x ' . $quiltsRow['quilt_height'] . '</span></td>';
                echo '<td style="width: 10%;"><span class="quilts-moderate-meta">' . $quiltsRow['tile_width'] . ' x ' . $quiltsRow['tile_height'] . '</span></td>';
                echo '<td style="width: 14%;"><span class="quilts-moderate-meta">' . niceDate($quiltsRow['posted_on']) . '</span></td>';
                echo '<td style="width: 14%;"><span class="quilts-moderate-meta">' . niceDate($quiltsRow['modified_on']) . '</span></td>';
                echo '<td class="quilts-moderate-actions">';
                if ($quiltsPermissionsRow['active']) {
                    echo '<span class="chip quilts-moderate-status-active">Active</span>';
                    echo '<span class="chip">' . makePostLink('?a=quilt_moderator_active&i=' . $quiltsRow['id'], 'Deactivate') . '</span>';
                } else {
                    echo '<span class="chip quilts-moderate-status-inactive">Inactive</span>';
                    echo '<span class="chip">' . makePostLink('?a=quilt_moderator_active&i=' . $quiltsRow['id'], 'Activate') . '</span>';
                }
                if ($quiltsPermissionsRow['permission'] == 'root') {
                    $tilesCount = (new \Databases\Tiles())->countByQuilt($quiltsRow['id']);
                    $tilesPendingCount = (new \Databases\TilesPending())->countByQuilt($quiltsRow['id']);
                    if (!$tilesCount && !$tilesPendingCount) {
                        echo '<span class="chip">' . makePostLink('?a=quilt_delete&i=' . $quiltsRow['id'], 'Delete', 'Delete this quilt?') . '</span>';
                    }
                    if ($quiltsRow['finished'] == 0) {
                        echo '<span class="chip">' . makeLink('?s=quilt_edit&i=' . $quiltsRow['id'], 'Edit') . '</span>';
                    } else {
                        echo '<span class="chip">Edit</span>';
                    }
                    if ($quiltsRow['finished'] == 0) {
                        if (!$tilesPendingCount) {
                            $number = $quiltsRow['quilt_width'] * $quiltsRow['quilt_height'];
                            $tilesDisplayCount = (new \Databases\Tiles())->countByQuiltNotDeleted($quiltsRow['id']);
                            if ($number == $tilesDisplayCount) {
                                echo '<span class="chip">' . makePostLink('?a=quilt_finished&i=' . $quiltsRow['id'], 'Mark Finished') . '</span>';
                            } else {
                                echo '<span class="chip">Mark Finished</span>';
                            }
                        } else {
                            echo '<span class="chip">Mark Finished</span>';
                        }
                    } else {
                        echo '<span class="chip">Finished</span>';
                    }
                } else {
                    echo '<span class="chip">Moderator Only</span>';
                }
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="quilts-moderate-empty">No ' . safeAttr(strtolower($statusFilterLabel)) . ' quilts found for your account.</div>';
    }
} else {
    echo '<div class="quilts-moderate-empty">You are not moderating any quilts right now.</div>';
}
echo '</div>';

include('../include/parts/footer.php');
