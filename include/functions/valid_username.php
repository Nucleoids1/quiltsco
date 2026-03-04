<?php
//accepts any alpha numeric string that starts with a letter. (can have '_','-' and '.' in it)
function isValidUsername($str)
{
    return preg_match('/^[A-Za-z].[A-z0-9][\w\-\.\_]+$/', $str);
}
