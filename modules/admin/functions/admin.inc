<?php

require_once($FUNCTIONDIR."/database.inc");


function dbPrintLog($lines)
{
    $sql = "SELECT * FROM ";
    $sql .= "( SELECT * FROM ActivityLog ORDER BY LogEntryID DESC LIMIT ".$lines.")";
    $sql .= " sub ORDER BY LogEntryID ASC;";
    $results = DB::run($sql);
    echo '<div class="w3-row">';
    echo '<div class="w3-col s2 m2 l2">';
    echo 'Date';
    echo '</div>';
    echo '<div class="w3-col s1 m1 l1">';
    echo 'AccountID';
    echo '</div>';
    echo '<div class="w3-col s2 m2 l2">';
    echo 'Function';
    echo '</div>';
    echo '<div class="w3-col s7 m7 l7">';
    echo 'Query';
    echo '</div>';
    echo "</div>\n";

    foreach ($results as $line) {
        echo '<div class="w3-row">';
        echo '<div class="w3-col s2 m2 l2">';
        echo $line['Date'];
        echo '</div>';
        echo '<div class="w3-col s1 m1 l1">';
        echo $line['AccountID'];
        echo '</div>';
        echo '<div class="w3-col s2 m2 l2">';
        echo $line['Function'];
        echo '</div>';
        echo '<div class="w3-col s7 m7 l7">';
        echo $line['Query'];
        echo '</div>';
        echo "</div>\n";
    }

}
