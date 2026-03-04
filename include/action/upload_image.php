<?php
    require_once('../include/functions/image_upload.php');

    $_typeVote = postInt('type_vote');

    if ($GLOBALS['auth']['id'])
    {
        list($new, $dupe, $notice) = processUploads();
        if ($new)
        {
            (new \Databases\GalleryImages())->addToGallery($new, $GLOBALS['auth']['id']);
            if ($_typeVote)
            {
                voteImage($new, $_typeVote);
            }
        }
        elseif ($dupe)
        {
            $galleryImagesRows = (new \Databases\GalleryImages())->findByImageIdAndUserId($dupe, $GLOBALS['auth']['id']);
            if (!count($galleryImagesRows))
            {
                (new \Databases\GalleryImages())->addToGallery($dupe, $GLOBALS['auth']['id']);
                if ($_typeVote)
                {
                    voteImage($dupe, $_typeVote);
                }
            }
            else
            {
                $notice = 'Duplicate Image';
            }
        }
        makeCookie('notice', $notice);
        header('Location: ./?s=upload_image');
        die;
    }
    else
    {
        header('Location: ./');
        die;
    }

    function voteImage($id, $theVote)
    {
        $imagesCategoriesRow = (new \Databases\ImagesCategories())->findById($theVote);
        if ($imagesCategoriesRow)
        {
            (new \Databases\ImagesCategoriesRating())->upsertRating($id, $theVote, $GLOBALS['auth']['id']);
        }
    }
