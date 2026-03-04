<?php
function tileIsAvailable($id, $x, $y, $zoom = 1, $noadmin = 0)
{
    $quiltsRow = (new \Databases\Quilts())->findById($id);
    if ($quiltsRow) {
        $admin = 0;
        if ($noadmin == 0) {
            if ((new \Databases\QuiltsPermissions())->hasActivePermissionByUserAndQuilt($GLOBALS['auth']['id'], $id)) {
                $admin = 1;
            }
        }
        $displayAvailable = 0;
        $displayHidden = 0;
        $displayNone = 0;
        $displayPending = 0;
        $displayPendingApproval = 0;
        $displayTile = 0;
        $displayUnavailable = 0;
        //DO THE WRAP AROUND
        $xPlus = $x + 1;
        $xMinus = $x - 1;
        $yPlus = $y + 1;
        $yMinus = $y - 1;
        if ($quiltsRow['edge_wrap'] == 1 || $quiltsRow['edge_wrap'] == 3) {
            if ($xPlus > $quiltsRow['quilt_width']) {
                $xPlus = 1;
            }
            if ($xMinus < 1) {
                $xMinus = $quiltsRow['quilt_width'];
            }
        }
        if ($quiltsRow['edge_wrap'] == 2 || $quiltsRow['edge_wrap'] == 3) {
            if ($yPlus > $quiltsRow['quilt_height']) {
                $yPlus = 1;
            }
            if ($y < 1) {
                $yMinus = $quiltsRow['quilt_height'];
            }
        }
        if ($y < 1 || $x < 1 || $x > $quiltsRow['quilt_width'] || $y > $quiltsRow['quilt_height']) {
            $displayNone = 1;
        } elseif ($quiltsRow['finished']) {
            $tileMiddleCenter = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($id, $x, $y);
            if ($tileMiddleCenter['visibility'] == -1) {
                $displayPendingApproval = 1;
            } else {
                $displayTile = 1;
            }
        } else {
            //CHECK IS THE QUILT HAS NOT BEEN STARTED YET AND LET ANY TILE BE AVAILABLE
            $numTile = (new \Databases\Tiles())->countByQuiltNotDeleted($id);
            $numPending = (new \Databases\TilesPending())->countByQuilt($id);
            if ($numTile || $numPending) {
                //LOAD SURROUNDING TILES INTO MEMORY
                $tileTopCenter = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($id, $x, $yMinus);
                $tileMiddleLeft = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($id, $xMinus, $y);
                $tileMiddleCenter = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($id, $x, $y);
                $tileMiddleRight = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($id, $xPlus, $y);
                $tileBottomCenter = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($id, $x, $yPlus);
                //LOAD PENDING TILES INTO MEMORY
                $pendingTopCenter = (new \Databases\TilesPending())->findByQuiltAndMatrix($id, $x, $yMinus);
                $pendingMiddleLeft = (new \Databases\TilesPending())->findByQuiltAndMatrix($id, $xMinus, $y);
                $pendingMiddleCenter = (new \Databases\TilesPending())->findByQuiltAndMatrix($id, $x, $y);
                $pendingMiddleRight = (new \Databases\TilesPending())->findByQuiltAndMatrix($id, $xPlus, $y);
                $pendingBottomCenter = (new \Databases\TilesPending())->findByQuiltAndMatrix($id, $x, $yPlus);
                //PROCESS EVERYTHING
                if ($tileMiddleCenter) {
                    if ($admin == 1 || $tileMiddleCenter['visibility'] == 1 || $quiltsRow['show_all'] == 1 || ($tileMiddleCenter['user_id'] == $GLOBALS['auth']['id'] && $noadmin == 0)) {
                        $displayTile = 1;
                    } elseif ($tileMiddleCenter['visibility'] == -1) {
                        $displayPendingApproval = 1;
                    } elseif ($quiltsRow['show_all'] == 2) {
                        $displayHidden = 1;
                    } elseif ($pendingTopCenter || $pendingMiddleLeft || $pendingMiddleRight || $pendingBottomCenter) {
                        $displayHidden = 1;
                    } elseif (($yMinus >= 1 && !$tileTopCenter) || ($xMinus >= 1 && !$tileMiddleLeft) || ($xPlus <= $quiltsRow['quilt_width'] && !$tileMiddleRight) || ($yPlus <= $quiltsRow['quilt_height'] && !$tileBottomCenter)) {
                        $displayHidden = 1;
                    } else {
                        $displayTile = 1;
                    }
                } elseif ($pendingMiddleCenter) {
                    $displayPending = 1;
                } else {
                    if ($pendingTopCenter || $pendingMiddleLeft || $pendingMiddleRight || $pendingBottomCenter) {
                        $displayUnavailable = 1;
                    } elseif (isset($tileTopCenter['visibility']) && $tileTopCenter['visibility'] == -1) {
                        $displayUnavailable = 1;
                    } elseif (isset($tileMiddleLeft['visibility']) && $tileMiddleLeft['visibility'] == -1) {
                        $displayUnavailable = 1;
                    } elseif (isset($tileMiddleRight['visibility']) && $tileMiddleRight['visibility'] == -1) {
                        $displayUnavailable = 1;
                    } elseif (isset($tileBottomCenter['visibility']) && $tileBottomCenter['visibility'] == -1) {
                        $displayUnavailable = 1;
                    } else {
                        if ($quiltsRow['start_anywhere'] && $quiltsRow['work_on_all']) {
                            $displayAvailable = 1;
                        } elseif ($quiltsRow['start_anywhere']) {
                            if (($tileTopCenter && $tileTopCenter['user_id'] == $GLOBALS['auth']['id']) || ($tileMiddleLeft && $tileMiddleLeft['user_id'] == $GLOBALS['auth']['id']) || ($tileMiddleRight && $tileMiddleRight['user_id'] == $GLOBALS['auth']['id']) || ($tileBottomCenter && $tileBottomCenter['user_id'] == $GLOBALS['auth']['id'])) {
                                $displayUnavailable = 1;
                            } else {
                                $displayAvailable = 1;
                            }
                        } elseif ($quiltsRow['work_on_all']) {
                            if ($tileTopCenter || $tileMiddleLeft || $tileMiddleRight || $tileBottomCenter) {
                                $displayAvailable = 1;
                            } else {
                                $displayUnavailable = 1;
                            }
                        } else {
                            if ($tileTopCenter || $tileMiddleLeft || $tileMiddleRight || $tileBottomCenter) {
                                if (($tileTopCenter && $tileTopCenter['user_id'] == $GLOBALS['auth']['id']) || ($tileMiddleLeft && $tileMiddleLeft['user_id'] == $GLOBALS['auth']['id']) || ($tileMiddleRight && $tileMiddleRight['user_id'] == $GLOBALS['auth']['id']) || ($tileBottomCenter && $tileBottomCenter['user_id'] == $GLOBALS['auth']['id'])) {
                                    $displayUnavailable = 1;
                                } else {
                                    $displayAvailable = 1;
                                }
                            } else {
                                $displayUnavailable = 1;
                            }
                        }
                    }
                }
                if ($displayAvailable) {
                    //CHECK IF YOU HAVE USED BY THE MAXIMUM TILES YOU CAN WORK ON FOR THIS QUILT
                    $usedPending = (new \Databases\TilesPending())->countByQuiltAndUser($id, $GLOBALS['auth']['id']);
                    if ($usedPending >= $quiltsRow['multiple']) {
                        $displayAvailable = 0;
                        $displayUnavailable = 1;
                    }
                }
            } else {
                $displayAvailable = 1;
            }
        }
        $temp = $x + $y;
        $color = ($temp % 2 == 0) ? '#2E3336' : '#303538';
        $textsize = round(12 * (1 * $zoom), 0);
        $textSizeRem = round($textsize / 12, 4);
        //RETURN THE RESULTS
        if ($displayAvailable) {
            if ($GLOBALS['auth']['id']) {
                return array(1, 0, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . ';">' . makeLink('?s=tile_checkout&i=' . $id . '&x=' . $x . '&y=' . $y, 'Available') . '</td>');
            } else {
                return array(0, 0, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . ';">Available</td>');
            }
        } elseif ($displayHidden) {
            return array(0, 0, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . ';">Completed By<br />' . makeLink('?s=profile&u=' . getUsername($tileMiddleCenter['user_id']), getUsername($tileMiddleCenter['user_id'])) . '<br />Hidden Until<br />Surrounding Tiles<br />Are Complete</td>');
        } elseif ($displayNone) {
            return array(0, 0, '<td></td>');
        } elseif ($displayPending) {
            return array(0, 0, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . ';">In Use By<br />' . makeLink('?s=profile&u=' . getUsername($pendingMiddleCenter['user_id']), getUsername($pendingMiddleCenter['user_id'])) . '</td>');
        } elseif ($displayPendingApproval) {
            return array(0, 0, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . ';">Completed By<br />' . makeLink('?s=profile&u=' . getUsername($tileMiddleCenter['user_id']), getUsername($tileMiddleCenter['user_id'])) . '<br />Pending<br />Approval<br /></td>');
        } elseif ($displayTile) {
            return array(0, 1, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . '; padding: 0;"><a href="?s=tile&i=' . $tileMiddleCenter['tile_id'] . '"><img src="?g=tile&i=' . $tileMiddleCenter['tile_id'] . '" title="' . safeAttr(getUsername($tileMiddleCenter['user_id'])) . ': ' . safeAttr($tileMiddleCenter['comment']) . '" alt="" style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; border: 0px; display: block;"></a></td>');
        } elseif ($displayUnavailable) {
            return array(0, 0, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . ';">Unavailable</td>');
        } else {
            return array(0, 0, '<td style="width: ' . round($quiltsRow['tile_width'] * (1 * $zoom), 0) . 'px; height: ' . round($quiltsRow['tile_height'] * (1 * $zoom), 0) . 'px; font-size: ' . $textSizeRem . 'rem; text-align: center; background: ' . $color . ';">Error</td>');
        }
    } else {
        return array(0, 0, '<td style="width: 100px; height: 100px; text-align: center;">Error</td>');
    }
}
