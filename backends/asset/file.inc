<?php
/*
    File base asset manager
 */

/*.
    require_module 'standard';
    require_module 'gd';
.*/

namespace ciab\backend;

class Asset
{


    public function __construct()
    {
        if (!array_key_exists('FILE_ASSET_PATH', $GLOBALS)) {
            error_log('File asset path not configured. Failing to load.');
        }

    }


    public function configuration()
    {
        $support = array();
        $types = \imagetypes();
        if (is_int(IMG_BMP) && $types & IMG_BMP) {
            $support[] = \image_type_to_mime_type(IMAGETYPE_BMP);
            $support[] = 'image/bmp';
        }
        if (is_int(IMG_GIF) && $types & IMG_GIF) {
            $support[] = \image_type_to_mime_type(IMAGETYPE_GIF);
        }
        if (is_int(IMG_JPG) && $types & IMG_JPG) {
            $support[] = \image_type_to_mime_type(IMAGETYPE_JPEG);
        }
        if (is_int(IMG_PNG) && $types & IMG_PNG) {
            $support[] = \image_type_to_mime_type(IMAGETYPE_PNG);
        }
        if (is_int(IMG_WBMP) && $types & IMG_WBMP) {
            $support[] = \image_type_to_mime_type(IMAGETYPE_WBMP);
        }
        if (is_int(IMG_WEBP) && $types & IMG_WEBP) {
            $support[] = \image_type_to_mime_type(IMAGETYPE_WEBP);
        }
        $config = array('FILE_ASSET_PATH' => [
                      'description' => 'Path to asset data (string)',
                      'value' => $GLOBALS['FILE_ASSET_PATH'],
                      'readonly' => false
                     ],
                     'FILE_ASSET_DATA' => [
                      'description' => 'Path is not web accessable (0 or 1)',
                      'value' => $GLOBALS['FILE_ASSET_DATA'],
                      'readonly' => false
                     ],
                     'ASSET_TYPES' => [
                      'description' => 'Image types that can be imported',
                      'value' => implode(',', $support),
                      'readonly' => true
                     ]
                );
        return $config;

    }


    public function load(/*.string.*/ $id)
    {
        if (array_key_exists('FILE_ASSET_DATA', $GLOBALS) &&
            $GLOBALS['FILE_ASSET_DATA']) {
            $data = @file_get_contents($GLOBALS['FILE_ASSET_PATH'].'/'.$id.'.png');
            $base64 = base64_encode($data);
            return ("data:image/png;base64,".$base64);
        } elseif (array_key_exists('FILE_ASSET_PATH', $GLOBALS)) {
            return (
            $GLOBALS['FILE_ASSET_PATH'].'/'.$id.'.png?'.md5_file(
                $GLOBALS['FILE_ASSET_PATH'].'/'.$id.'.png'
            ));
        } else {
            return "";
        }

    }


    public function save(/*.string.*/ $id, /*.string.*/ $data)
    {
        $image = @\imagecreatefromstring($data);
        $file = $GLOBALS['FILE_ASSET_PATH'].'/'.$id.'.png';
        @\imagepng($image, $file);
        \imagedestroy($image);

    }


    public function exists(/*.string.*/ $id)
    {
        if (!array_key_exists('FILE_ASSET_PATH', $GLOBALS)) {
            return false;
        }
        $file = $GLOBALS['FILE_ASSET_PATH'].'/'.$id.'.png';
        return file_exists($file);

    }


    /* end class */
}
