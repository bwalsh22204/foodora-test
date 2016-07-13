# Foodora Test

My solution consists of two scripts:

- 'merge.php' backs up the existing regular schedule and merges the special day scheduling.
- 'restore.php' restores the regular schedule from the backup, eliminating any special day scheduling. 

## Instructions

First, you must edit the `db-info.php` file. You should only need to change `$dbname`, `$username`, and `$password`. Then, you may run the scripts.

To initiate the backup and merging, run `php merge.php` (as on Dec. 20th, 2015).

To restore the regular schedule, run `php restore.php` (as on Dec. 28th, 2015).

## Limitations

The merge script relies on the fact that it is only expected to operate on the dates 21-27 December, 2015, which correspond directly to schedule days 1-7 (Monday - Sunday).
