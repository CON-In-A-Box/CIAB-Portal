<?php

/*.
    require_module 'standard';
.*/

require_once($FUNCTIONDIR."/database.inc");


function filterLog($query)
{
    $pattern = ["/(Authentication = ')(.*)(')/i", "/(OneTime = ')(.*)(')/i"];
    $replace = '\1&lt;REDACTED&gt;\3';
    return(preg_replace($pattern, $replace, $query));

}


function dbPrintLog($lines)
{
    $sql = "SELECT * FROM ";
    $sql .= "( SELECT * FROM ActivityLog ORDER BY LogEntryID DESC LIMIT ".$lines.")";
    $sql .= " sub ORDER BY LogEntryID DESC;";
    $results = DB::run($sql);
    echo '<div class="ADMIN-log-row">';
    echo '<div class="ADMIN-log-col-date">';
    echo 'Date';
    echo '</div>';
    echo '<div class="ADMIN-log-col-id">';
    echo 'AccountID';
    echo '</div>';
    echo '<div class="ADMIN-log-col-function">';
    echo 'Function';
    echo '</div>';
    echo '<div class="ADMIN-log-col-query">';
    echo 'Query';
    echo '</div>';
    echo "</div>\n";

    foreach ($results as $line) {
        echo '<div class="ADMIN-log-row">';
        echo '<div class="ADMIN-log-col-date">';
        echo $line['Date'];
        echo '</div>';
        echo '<div class="ADMIN-log-col-id">';
        echo $line['AccountID'];
        echo '</div>';
        echo '<div class="ADMIN-log-col-function">';
        echo $line['Function'];
        echo '</div>';
        $query = filterLog($line['Query']);
        echo '<div class="ADMIN-log-col-query" title="'.$query.'">';
        if (strlen($query) > 100) {
            echo "<p class='UI-tooltip'>".substr($query, 0, 100)." \u{2026}";
            echo "<span class='UI-tooltip-text'>$query</span></p>";
        } else {
            echo $query;
        }
        echo '</div>';
        echo "</div>\n";
    }

}


function get_config()
{
    $output = [];
    $sql = "SELECT * FROM `Configuration`;";
    $results = DB::run($sql);
    foreach ($results as $line) {
        $entry = $line;
        $entry['Source'] = 'DB';
        $output[$line['Field']]  = $entry;
    }
    foreach ($_ENV as $key => $entry) {
        $output[$key] = ['Field' => $key, 'Source' => 'ENV'];
    }
    ksort($output);
    return $output;

}


function rppg()
{
    $pw = '';
    $c  = 'bcdfghjklmnprstvwz'; // consonants except hard to speak ones
    $v  = 'aeiou';              // vowels
    $a  = $c.$v;                // all

  //use two syllables...
    for ($i = 0; $i < 3; $i++) {
        $sy = $c[rand(0, strlen($c) - 1)];
        $sy .= $v[rand(0, strlen($v) - 1)];
        $sy .= $a[rand(0, strlen($a) - 1)];
        if (rand(0, 1) == 0) {
            $sy = ucfirst($sy);
        }
        $pw .= $sy;
    }
  //... and add a nice number
    $pw .= rand(10, 99);

    return $pw;

}
