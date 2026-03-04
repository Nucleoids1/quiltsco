<?php
    require_once('../include/functions/pages.php');

    $_id = getInt('i');
    $_page = getInt('p', 1);

    $galleryImagesCount = (new \Databases\GalleryImages())->countByUserId($_id);
    $galleryImagesPageCount = max(1, intval(ceil($galleryImagesCount / 25)));

    echo '<div class="header">Graphix Completed<br />Page: ' . pagesAjax('javascript:graphixCompleted(%%PAGE%%);', $_page, $galleryImagesPageCount) . '</div>';
    echo '<div class="content" style="padding: 0px 5px 5px 5px;">';

    $galleryImagesRows = (new \Databases\GalleryImages())->findPageByUserId($_id, 25, ($_page - 1) * 25);
    if (count($galleryImagesRows))
    {
        echo '<table role="presentation" style="width: 100%; border-spacing: 0;"><tr><td style="padding: 0;">';
        foreach ($galleryImagesRows as $galleryImagesRow)
        {
            $imagesCommentsCount = (new \Databases\Comments('images_comments'))->countByLinkId($galleryImagesRow['image_id']);
            echo boxImageTop();
            echo '<table role="presentation" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border-spacing: 0;"><tr><td style="padding: 0; text-align: center; vertical-align: top;">';
            echo '<a href="?s=image&i=' . $galleryImagesRow['image_id'] . '"><img src="?g=thumb&i=' . $galleryImagesRow['image_id'] . '" alt="Gallery image by ' . safeAttr(getUsername($_id)) . '" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: 0px;"></a><br />';
            echo '<div style="padding: 4px 0px 0px 0px;"></div>';
            echo '<b>' . $imagesCommentsCount . '</b> Comments<br />';
            echo '</td></tr></table>';
            echo boxImageBottom();
        }
        echo '</td></tr></table>';
    }
    else
    {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>This member has not completed any graphix...';
    }
    echo '</div>';
