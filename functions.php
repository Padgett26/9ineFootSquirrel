<?php
session_start();

include "../globalFunctions.php";

$db = db_nfs();

$time = time();
$domain = "9inefootsquirrel.com";
$monthNames = array(
        "0",
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
);

function delTree ($dir)
{
    $files = array_diff(scandir($dir), array(
            '.',
            '..'
    ));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}