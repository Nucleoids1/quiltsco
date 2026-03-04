<?php
function tableExists($tableName)
{
    return (new \Databases\Images())->tableExistsInMaster($tableName);
}
