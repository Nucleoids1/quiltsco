<?php
    //HOST
    define('HOST_NAME', 'quiltsco.com');
    define('APP_BASE_URL', 'https://www.quiltsco.com');
    define('SHUT_SITE_DOWN', 0);

    //SQL INFO
    define('SQL_HOST', '127.0.0.1');
    define('SQL_LOGIN', '');
    define('SQL_PASSWORD', '');
    define('SQL_DATABASE', '');
    // Optional environment override: TEST_DB_NAME=quiltsco_test

    //IP STUFF
    define('IP_BITS', 14);
    define('SHIFTBITS', intval(32 - IP_BITS));

    //SMTP
    define('SMTP_HOST', 'smtp-relay.brevo.com');
    define('SMTP_PORT', 587);
    define('SMTP_LOGIN', '');
    define('SMTP_PASSWORD', '');
    define('SMTP_ENCRYPTION', 'starttls');
    define('SMTP_FROM_EMAIL', '');

    //QUILTS
    define('SIDE_PIXELS', 32);

    //PHOTOS
    define('MAX_PICTURE_UPLOAD_SIZE', 5000000);
    define('NEW_PICTURE_TYPE', 'png');
    define('THUMB_WIDTH', 160);
    define('THUMB_HEIGHT', 120);
    define('IMAGE_MAX_WIDTH', 800);
    define('IMAGE_MAX_HEIGHT', 3000);
    define('IMAGES_IN_PROFILE', 15);
    define('IMAGES_PER_PAGE', 20);

    //PER PAGE
    define('PHOTOS_PER_PAGE', 10);
    define('ACTIVITIES_PER_PAGE', 10);
    define('FAVORITES_PER_PAGE', 10);
    define('MESSAGES_PER_PAGE', 10);
    define('MAIL_USERS_PER_PAGE', 10);
    define('RESULTS_PER_PAGE', 10);
    define('USERS_PER_PAGE', 50);

    //COMMUNITY
    define('COMMUNITY_DEFAULT_ID', 1);
    define('COMMUNITY_NAME_MIN', 4);
    define('COMMUNITY_NAME_MAX', 32);
    define('COMMUNITY_SECTION_NAME_MIN', 4);
    define('COMMUNITY_SECTION_NAME_MAX', 32);
    define('COMMUNITY_FORUM_NAME_MIN', 4);
    define('COMMUNITY_FORUM_NAME_MAX', 32);
    define('COMMUNITY_FORUM_DESC_MIN', 0);
    define('COMMUNITY_FORUM_DESC_MAX', 100);

    define('COMMUNITY_THREAD_TITLE_MAX', 70);
    define('COMMUNITY_THREAD_BODY_MAX', 10000);
    define('COMMUNITY_REPLY_BODY_MAX', 10000);

    define('COMMUNITY_THREADS_PER_PAGE', 20);
    define('COMMUNITY_REPLIES_PER_PAGE', 20);
    //MAKE IT LESS THAN COMMUNITY_THREADS_PER_PAGE
    define('COMMUNITY_STICKIES_MAX', 5);

    //USER INFO
    define('USERNAME_MIN', 4);
    define('USERNAME_MAX', 16);
    define('PASSWORD_MIN', 6);
    define('PASSWORD_MAX', 40);
    define('EMAIL_MAX', 70);
    define('SIGNATURE_MIN', 3);
    define('SIGNATURE_MAX', 140);

    //COMMENTS
    define('COMMENT_MAX_LENGTH', 1024);

    //TOKEN TTL (24 hours)
    define('MEMBERS_CREATE_TOKEN_TTL_SECONDS', 60 * 60 * 24);
