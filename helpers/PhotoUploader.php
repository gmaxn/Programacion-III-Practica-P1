<?php

class PhotoUploader {

    public $datetime;
    public $saveDirectory;

    function __construct($saveDirectory) {

        $this->saveDirectory = $saveDirectory;
        $this->datetime = date("dmY_gia");
    }

    public static function addWaterMark($target, $wtrmrk_file, $newcopy, $unlinkTarget = false) {

        $watermark = imagecreatefrompng($wtrmrk_file);
        imagealphablending($watermark, false);
        imagesavealpha($watermark, true);

        if(exif_imagetype($target) == IMAGETYPE_PNG) {
            $img = imagecreatefrompng($target);
        }

        if(exif_imagetype($target) == IMAGETYPE_JPEG) {
            $img = imagecreatefromjpeg($target);
        }

        $img_w = imagesx($img);
        $img_h = imagesy($img);
        $wtrmrk_w = imagesx($watermark);
        $wtrmrk_h = imagesy($watermark);
        $dst_x = ($img_w / 2) - ($wtrmrk_w / 2); // For centering the watermark on any image
        $dst_y = ($img_h / 2) - ($wtrmrk_h / 2); // For centering the watermark on any image
        imagecopy($img, $watermark, $dst_x, $dst_y, 0, 0, $wtrmrk_w, $wtrmrk_h);
        imagejpeg($img, $newcopy, 100);
        imagedestroy($img);
        imagedestroy($watermark);
    
        if($unlinkTarget  && $target != $newcopy) {
            unlink($target);
        }
    }

    public static function crop($target, $dest, $newSizeX, $newSizeY, $unlinkTarget = false) {
        
        if(exif_imagetype($target) == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($target);
        }

        if(exif_imagetype($target) == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($target);
        }    
        $image_width = imagesx($image);
        $image_height = imagesy($image);
    
        $image2 = imagecrop($image, ['x' => ($image_width / 2) - ($newSizeX / 2), 'y' => ($image_height / 2) - ($newSizeY / 2), 'width' => $newSizeX, 'height' => $newSizeY]);
    
        imagejpeg($image2, $dest, 100);
    
        imagedestroy($image);
        imagedestroy($image2);
    
        if($unlinkTarget && $target != $dest) {
            unlink($target);
        }
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