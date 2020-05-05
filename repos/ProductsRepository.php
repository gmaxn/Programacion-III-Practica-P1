<?php

class ProductsRepository
{
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
    public static function saveJSON($filename, $data)
    {
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
    public static function saveCSV($filename, $data)
    {

        $file = fopen($filename, 'a');
        $result = fwrite($file, $data);
        fclose($file);
        return $result ?? false;
    }
    public static function readSerialized($filename)
    {

        if (!file_exists($filename)) {
            throw new Exception('File not found');
        }

        $file = fopen($filename, 'r');
        $stream = fread($file, filesize($filename));
        $list = unserialize($stream);
        fclose($file);

        return $list ?? false;
    }
    public static function readJSON($filename)
    {
        if (!file_exists($filename)) {

            throw new Exception('File not found');
        }

        $file = fopen($filename, 'r');
        $stream = fread($file, filesize($filename));
        $list = json_decode($stream);
        fclose($file);

        $array = array();
        foreach ($list as $product) {
            array_push(
                $array,
                new Product(
                    $product->type,
                    $product->brand,
                    $product->price,
                    $product->stock,
                    $product->image,
                    $product->id
                )
            );
        }

        return $array ?? false;
    }
    public static function readCSV($filename)
    {
        if (!file_exists($filename)) {

            throw new Exception('File not found');
        }

        $dataSet = array();
        $file = fopen($filename, 'r');

        while (!feof($file)) {

            $row = fgets($file);
            $exploded = explode(',', $row);

            $i = 0;
            foreach ($exploded as $str) {

                $exploded[$i] = trim($str);
                $i++;
            }

            if ($row != '') {

                array_push($dataSet, $exploded);
            }
        }

        $list = self::rawCSVSerializer($dataSet);

        fclose($file);

        return $list;
    }
    public static function updateSerialized($filename, $list)
    {
        if (file_exists($filename)) {

            $file = fopen($filename, 'w');
            $result = fwrite($file, serialize($list));
            fclose($file);
        }

        return $result ?? false;
    }
    public static function updateJSON($filename, $list)
    {
        $file = fopen($filename, 'w');
        $result = fwrite($file, json_encode($list));
        fclose($file);

        return $result ?? false;
    }
    public static function updateCSV($filename, $data)
    {
        $file = fopen($filename, 'a');
        $result = fwrite($file, $data);
        fclose($file);
        return $result ?? false;
    }
    private static function rawCSVSerializer($dataSet)
    {
        $result = array();

        foreach ($dataSet as $data) {

            $persona = new Product(
                $data[1],
                $data[2],
                $data[3],
                $data[4],
                $data[5],
                $data[0]
            );

            array_push($result, $persona);
        }

        return $result;
    }
}
