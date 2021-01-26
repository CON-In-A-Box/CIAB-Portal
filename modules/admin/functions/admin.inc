<?php

/*.
    require_module 'standard';
.*/

require_once($FUNCTIONDIR."/database.inc");


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
