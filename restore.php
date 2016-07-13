<?php

# reset database - run on Dec. 28th 2015 after special days have passed

require_once 'db-info.php';

try
{
    $db = new PDO($dsn, $username, $password, $options);

    # ensure backup table exists before proceeding
    $db->query("SELECT 1 FROM " . $backupTableName . " LIMIT 1");
    
    # clear regular schedule table (which now contains special day entries)
    $db->query("TRUNCATE TABLE " . $scheduleTableName);

    # restore regular schedule entries from backup table
    $db->query("INSERT " . $scheduleTableName . " SELECT * FROM ". $backupTableName);

    # delete backup table
    $db->query("DROP TABLE IF EXISTS `" . $backupTableName . "`");

    echo "Regular schedule successfully restored!\n";
}
catch (Exception $e)
{
    echo $e->getMessage();
}
