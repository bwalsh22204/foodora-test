<?php

$dbname = 'foodoratest'; # database name

# user should have full priviliges for database
$username = 'foodora';
$password = 'foodora';

$vendor = 'vendor'; # 'vendor' table name
$scheduleTableName = 'vendor_schedule'; # 'vendor_schedule' table name
$backupTableName = 'vendor_backup_schedule'; # 'backup_schedule' table name
$specialDay = 'vendor_special_day'; # 'vendor_special_day' table name


########

$dsn = 'mysql:host=localhost;dbname=' . $dbname;
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);