<?php

require_once __DIR__ . '\..\repos\ProductsRepository.php';


class Product {

    public $id;
    public $type;
    public $brand;
    public $price;
    public $stock;
    public $image;

    public function getImageName($image) {

        if($image != null) {
            
            return getenv('DEFAULT_IMAGE_DIR') . $this->toFilename('.png');
        }

        return getenv('DEFAULT_IMAGE_DIR') . '\default_product.png';
    }

    public function __construct($type, $brand, $price, $stock, $image, $id = null) {

        $this->id = ($id ?? strtotime('now'));
        $this->type = $type;
        $this->brand = $brand;
        $this->price = $price;
        $this->stock = $stock;
        $this->image = $this->getImageName($image);
    }

    public function save($saveType = 'Serialized') {

        switch($saveType)
        {
            case 'Serialized':
                $filename = __DIR__ . '\..\data\products.txt';
                ProductsRepository::saveSerialized($filename, $this);
            break;
     
            case 'JSON':
                $filename = __DIR__ . '\..\data\products.json';
                ProductsRepository::saveJSON($filename, $this->toJSON());
            break;

            case 'CSV':
                $filename = __DIR__ . '\..\data\products.csv';
                ProductsRepository::saveCSV($filename, $this->toCSV());
            break;

            default:
                throw new Exception('Incompatible save type exception: check line 71 ProductController.php');
                ProductsRepository::saveSerialized($filename, $this);
            break;
        }
    }

    public static function validate($productDto) {

        $result = new ValidationResult();

        foreach($productDto as $key => $value) {

            if($value == null || $value == '')
            {
                $result->isValid = false;
                $result->errorMessage = $key . ' is null or empty';
                $result->status = 'failure';

                return $result;
            }
        }

        $result->isValid = true;
        $result->errorMessage = null;
        $result->status = 'succeed';

        return $result;
    }

    public function toJSON() {

        return json_encode($this);
    }

    public function toCSV() {

        return $this->id . ',' . 
               $this->type . ',' . 
               $this->brand . ',' . 
               $this->price . ',' . 
               $this->stock . ',' . 
               $this->image . PHP_EOL; 
    }

    public function toFilename($ext) {

        return '\\' . $this->type . '-' .
                      $this->id . '-' .
                      $this->brand . '-' .
                      date("dmY_gia") . $ext;
    }
}