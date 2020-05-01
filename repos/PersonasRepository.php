<?php

class PersonasRepository {

    public static function saveSerialized($filename, $data)
    {
        $list = array();

        if (file_exists($filename)) {

            $file = fopen($filename, 'r');
            $stream = fread($file, filesize($filename));
            $list = unserialize($stream);
            fclose($file);
        }

        array_push($list, $data);

        $file = fopen($filename, 'w');
        $result = fwrite($file, serialize($list));
        fclose($file);

        return $result ?? false;
    }

    public static function saveJSON($filename, $data) {

        $list = array();

        if (file_exists($filename)) {

            $file = fopen($filename, 'r');
            $stream = fread($file, filesize($filename));
            $list = json_decode($stream);
            fclose($file);
        }
        
        array_push($list, json_decode($data));
        
        $file = fopen($filename, 'w');
        $result = fwrite($file, json_encode($list));
        fclose($file);
        
        return $result ?? false;
    }

    public static function saveCSV($filename, $data) {

        $file = fopen($filename, 'a');
        $result = fwrite($file, $data);
        fclose($file);
        return $result ?? false;
    }
}