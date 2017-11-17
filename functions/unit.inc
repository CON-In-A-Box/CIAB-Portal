<?php

$tests = 0;
$pass = 0;
$failed = 0;


function reset_tests()
{
    global $tests, $pass, $failed;
    $tests = 0;
    $pass = 0;
    $failed = 0;

}


function unit_ok($condition, $message)
{
    global $tests, $pass, $failed;
    $tests += 1;

    if (!$condition) {
        $failed += 1;
        $trace = debug_backtrace();
        print('FAILED:'.basename($trace[1]['file']).':'.$trace[1]['function'].':'.$trace[1]['line'].':'.$message."\n");
    } else {
        $pass += 1;
    }
    return $condition;

}


function report()
{
    global $tests, $pass, $failed;
    print ($tests." tests run:\n");
    print ("\t".$pass." passed\n");
    print ("\t".$failed." failed\n");

}
