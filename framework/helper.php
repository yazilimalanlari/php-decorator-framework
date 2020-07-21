<?php

function get_rand_code(int $limit = 10): string {
    $code = "";
    $characters = array_merge(range('a', 'z'), range(0, 9), array('-', '_'));
    for($i = 0; $i < $limit; $i++) {
        $code .= $characters[rand(0, count($characters)-1)];
    }
    return $code;
}


function file_write(string $path, string $data) {
    $file = fopen($path, "w");
    fwrite($file, $data);
    fclose($file);
}


function file_read(string $path) {
    $filesize = filesize($path);
    if($filesize > 0) {
        $file = fopen($path, "r");
        $code = fread($file, $filesize);
        fclose($file);
        return $code;
    } else {
        return "";
    }
}