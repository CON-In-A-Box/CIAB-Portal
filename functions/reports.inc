<?php
/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once(__DIR__.'/database.inc');


function array_to_csv($report)
{
    $output = "";
    $output .= implode(",", array_keys($report[0]));
    $output .= "\n";
    foreach ($report as $entry) {
        $output .= htmlspecialchars_decode(implode(",", $entry), ENT_QUOTES);
        $output .= "\n";
    }
    return $output;

}


function generate_csv_report($source)
{
    if (is_string($source)) {
        if (strstr($source, 'SELECT') !== false) {
            return dump_sql_to_csv($source, true);
        } else {
            return dump_table_to_csv($source);
        }
    } elseif (is_array($source)) {
        return array_to_csv($source);
    } else {
        return $source;
    }

}


function generate_csv_report_function($source)
{
    /*.string.*/ $data = null;
    try {
        if (func_num_args() > 1) {
            $func = $source;
            $arguments = func_get_args();
            array_shift($arguments);
            $data = @call_user_func_array($func, $arguments);
        } else {
            $data = @call_user_func($source);
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }
    return generate_csv_report($data);

}


function print_csv_report($filename, $source)
{
    csv_headers($filename);
    if (is_callable($source)) {
        $arguments = func_get_args();
        array_shift($arguments);
        try {
            print @call_user_func_array(
                'generate_csv_report_function',
                $arguments
            );
        } catch (Exception $e) {
            print generate_csv_report($source);
        }
    } else {
        print generate_csv_report($source);
    }

}


function handle_report_request(
    $reports,
    $countArg = 'availableReports',
    $reportArg = 'report'
) {
    if (!empty($_REQUEST)) {
        // Retrieve and sanitize GET data
        $arguments = [
        $countArg => FILTER_SANITIZE_SPECIAL_CHARS,
        $reportArg => FILTER_SANITIZE_SPECIAL_CHARS,
        ];

        $updateData = filter_input_array(INPUT_GET, $arguments);

        if (isset($updateData[$countArg])) {
            $keys = array_keys($reports);
            sort($keys);
            print json_encode($keys);
            exit();
        }

        if (isset($updateData[$reportArg])) {
            $arguments = [];
            $arguments[] = $updateData[$reportArg].'.csv';
            if (is_array($reports[$updateData[$reportArg]])) {
                $arguments = array_merge($arguments, $reports[$updateData[$reportArg]]);
            } else {
                $arguments[] = $reports[$updateData[$reportArg]];
            }
            @call_user_func_array(
                'print_csv_report',
                $arguments
            );
            exit();
        }
    }

}
