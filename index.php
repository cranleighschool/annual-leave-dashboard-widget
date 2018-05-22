<?php

require_once 'vendor/autoload.php';

/* Load .env */
$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();
$dotenv->required(array('GOOGLE_CALENDAR_ICS_URI'));

/* File path or url of the .ics document */
$file = getenv("GOOGLE_CALENDAR_ICS_URI");

/* Getting events from isc file */
$calendar = new CranleighSchool\AnnualLeave\parseIcs($file);
echo '<h3 class="widgettitle">Annual Leave</h3>';
echo '<div class="leavedatablock">';
echo $calendar->displayTable();
echo '</div>';
