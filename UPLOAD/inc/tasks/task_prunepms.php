<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

// Prune old PMs
function task_pmprune($task)
{
    global $db;

    // PMs older than x seconds will be deleted
    $secs = 30*24*3600; // 30 days
    $usecs = 60*24*3600; // 60 days
    // Do not delete unread PMs
    $db->delete_query("privatemessages", "(dateline<".(TIME_NOW-$secs)." AND readtime>0) OR dateline<".(TIME_NOW-$usecs));
	// Recount PMs after cleaning
    $queryString  = <<<SQL
    UPDATE mybb_users u SET 
        totalpms = (SELECT COUNT(pmid) FROM mybb_privatemessages pm WHERE pm.uid = u.uid),
        unreadpms = (SELECT COUNT(pmid) FROM mybb_privatemessages pm WHERE
            pm.uid = u.uid AND status='0' AND folder='1');
SQL;

    $db->write_query($queryString);
} 