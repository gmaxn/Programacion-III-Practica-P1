<?php

class PhotoUploader {

    public $datetime;
    public $saveDirectory;

    function __construct($saveDirectory) {

        $this->saveDirectory = $saveDirectory;
        $this->datetime = date("dmY_gia");
    }

    public static function addWaterMark($source) {

    }

    public static function uploadPhoto($source, $dest) {

        return move_uploaded_file($source, $dest);
    }

    public function updatePhoto($source, $dest, $old) {

        // Gets old image and move to buck folder
        $exploded = explode('\\', $old);
        $buckdir = __DIR__ . '\..\Data\Photos\Buckup\\' . $exploded[count($exploded)-1];

        copy($old, $buckdir);
        unlink($old);

        // saves new image on temp folder
        $temppath = __DIR__ . '\..\Data\tmp\image.png';
        file_put_contents($temppath, $source);
        copy($temppath, $dest);
        unlink($temppath);

        return true;
    }

    public function removePhoto($path) {
        return unlink($path);
    }
}