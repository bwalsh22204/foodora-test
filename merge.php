<?php

# backup / special day overwrite - run on Dec. 20th 2015 before special days begin

require_once 'db-info.php';

try
{
    $db = new PDO($dsn, $username, $password, $options);

    ########
    # clone / backup vendor_schedule

    # delete backup table, if it already exists
    $db->query("DROP TABLE IF EXISTS `" . $backupTableName . "`");

    # create empty backup table
    $db->query("CREATE TABLE ". $backupTableName . " LIKE " . $scheduleTableName);

    # copy regular schedule entries to backup table
    $db->query("INSERT " . $backupTableName . " SELECT * FROM ". $scheduleTableName);

    echo "Regular schedule successfully backed up!\n";



    ########
    # overwrite vendor_schedule w/ special_day changes

    echo "Merging special days. Please be patient...\n";

    $vendorStatement = $db->query("SELECT id FROM " . $vendor);
    while ($vendorRow = $vendorStatement->fetch(PDO::FETCH_ASSOC)) {
        # translate special_day to vendor_schedule format using SpecialDayObj
        
        $specialStatement = $db->query("SELECT * FROM " . $specialDay . " WHERE vendor_id=" . $vendorRow['id']);
        $specialOne = new SpecialDayObj($specialStatement);
        
        for ($i=1; $i<=7; $i++) {

            $currentDay = $specialOne->getDay($i);
            if ($currentDay) { # special entry exists for this day
                $deleteStatement = $db->query(
                    "DELETE FROM " . $scheduleTableName . " WHERE vendor_id=" . $vendorRow['id'] . " AND weekday=" . $i
                );
                $insertValues = '';
                foreach ($currentDay as $newRow) {
                    if ($newRow['event_type'] == 'opened') {
                        $insertValues .= "(" . 
                            $newRow['vendor_id'] . "," . 
                            $newRow['weekday'] . "," . 
                            $newRow['all_day'] . ",'" . 
                            $newRow['start_hour'] . "','" . 
                            $newRow['stop_hour'] . "')" .
                        ',';
                    }
                }
                $insertValues = trim($insertValues, ',');
                if (strlen($insertValues) > 0) {
                    $insert = "INSERT " . $scheduleTableName . " (vendor_id,weekday,all_day,start_hour,stop_hour) VALUES " . $insertValues;
                    $insertStatement = $db->query($insert);
                }
            }
        } 
    }

    echo "Special days successfully merged into regular schedule!\n";
}
catch (Exception $e)
{
    echo $e->getMessage();
}



#    M  T  W  Th F  Sa Su
#    1  2  3  4  5  6  7

    # last digit of 'special_date' => 'weekday'

/* PROCEDURE - per vendor:
    1) assemble special days into object
        [for vendor N] SpecialDayObj - contains all special days for vendor N
        methods to check for 
    2) for each day of week (1-7), if special entry exists:
        - delete all entries for that day in vendor_schedule
        - insert special day entries into vendor_schedule

*/


# overwrite vendor_schedule w/ special_day changes
#   if there is ANY entry on a special day, remove ALL of the regular schedule for that day




########
class SpecialDayObj
{
    protected $days = array();

    # take result of DB query "SELECT * FROM $specialDay WHERE 'vendor_id'=N"
    # array(7) { 
    #   ["id"]=> string(1) "1"
    #   ["vendor_id"]=> string(1) "1"
    #   ["special_date"]=> string(10) "2015-12-24"
    #   ["event_type"]=> string(6) "closed" / "opened"
    #   ["all_day"]=> string(1) "1"
    #   ["start_hour"]=> NULL
    #   ["stop_hour"]=> NULL
    # }
    # Array ( [id] => 1 [vendor_id] => 1 [weekday] => 2 [all_day] => 0 [start_hour] => 19:00:00 [stop_hour] => 22:00:00 )

    public function __construct($dbResult)
    {
        while ($row = $dbResult->fetch(PDO::FETCH_ASSOC)) 
        {
            $specialEntry = array(
                'vendor_id' => $row['vendor_id'],
                'weekday' => (integer) substr($row['special_date'], -1),
                'event_type' => $row['event_type'],
                'all_day' => $row['all_day'],
                'start_hour' => $row['start_hour'],
                'stop_hour' => $row['stop_hour']
            );
            if ($this->days[$specialEntry['weekday']]) {
                array_push($this->days[$specialEntry['weekday']], $specialEntry);
            }
            else {
                $this->days[$specialEntry['weekday']] = array($specialEntry);
            }
        }
    }

    public function getDays()
    {
        return $this->days;
    }

    public function getDay($n)
    {
        return $this->days[$n];
    }
}
