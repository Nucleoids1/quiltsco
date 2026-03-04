<?php
function isValidEmail($str)
{
    return preg_match('/^[A-z0-9][\w.-]*@[A-z0-9][\w\-\.]+\.[A-z0-9]{2,6}$/', $str);
}
