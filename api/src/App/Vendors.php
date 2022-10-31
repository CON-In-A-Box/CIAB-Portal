<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/* setupAPIVendors */

function setupAPIVendors(array $settings)
{
    foreach ($settings['vendors'] as $vendor) {
        $files = [];

        if (is_array($vendor)) {
            $key = array_keys($vendor)[0];
            $target = __DIR__.'/../Vendors/'.$key;
            if (is_dir($target)) {
                $files = $vendor[$key];
            } else {
                error_log("Vendor $key not found");
            }
        } else {
            $target = __DIR__.'/../Vendors/'.$vendor;
            if (is_dir($target)) {
                $files = array_filter(
                    scandir($target),
                    function ($item) use ($target) {
                        $path = $target.'/'.$item;
                        $info = pathinfo($path);
                        return (is_file($path) && $info['extension'] == 'php');
                    }
                );
            } else {
                error_log("Vendor $target not found");
            }
        }

        foreach ($files as $file) {
            $path = $target.'/'.$file;
            if (is_file($path)) {
                require_once($path);
            } else {
                error_log("Vendor $path not able to be included");
            }
        }
    }

}
