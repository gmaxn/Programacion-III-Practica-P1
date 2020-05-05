<?php
require_once __DIR__ . '\..\repos\ProductsRepository.php';

class Product
{
    public $id;
    public $type;
    public $brand;
    public $price;
    public $stock;
    public $image;

    public function getImageName($image)
    {

        if ($image != null) {

            return getenv('DEFAULT_IMAGE_DIR') . $this->toFilename('.png');
        }

        return getenv('DEFAULT_IMAGE_DIR') . '\default_product.png';
    }
    public function __construct($type, $brand, $price, $stock, $image, $id = null)
    {

        $this->id = ($id ?? strtotime('now'));
        $this->type = $type;
        $this->brand = $brand;
        $this->price = (float) $price;
        $this->stock = (int) $stock;
        $this->image = $this->getImageName($image);
    }
    public function save()
    {

        $filename = getenv('PRODUCTS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch ($ext) {
            case 'TXT':
                ProductsRepository::saveSerialized($filename, $this);
                break;

            case 'JSON':
                ProductsRepository::saveJSON($filename, $this->toJSON());
                break;

            case 'CSV':
                ProductsRepository::saveCSV($filename, $this->toCSV());
                break;

            default:
                throw new Exception('Incompatible save type exception');
                break;
        }
    }
    public function update($list)
    {

        $filename = getenv('PRODUCTS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch ($ext) {
            case 'TXT':
                ProductsRepository::updateSerialized($filename, $list);
                break;

            case 'JSON':
                ProductsRepository::updateJSON($filename, $list);
                break;

            case 'CSV':
                ProductsRepository::updateCSV($filename, $list);
                break;

            default:
                throw new Exception('Incompatible save type exception');
                break;
        }
    }
    public static function getProductsList()
    {

        $filename = getenv('PRODUCTS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch ($ext) {
            case 'TXT':
                $list = ProductsRepository::readSerialized($filename);
                break;

            case 'JSON':
                $list = ProductsRepository::readJSON($filename);
                break;

            case 'CSV':
                $list = ProductsRepository::readCSV($filename);
                break;

            default:
                throw new Exception('Incompatible save type exception');
                break;
        }



        return $list;
    }
    public static function getProductById($productId)
    {

        $filename = getenv('PRODUCTS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch ($ext) {
            case 'TXT':
                $list = ProductsRepository::readSerialized($filename);
                break;

            case 'JSON':
                $list = ProductsRepository::readJSON($filename);
                break;

            case 'CSV':
                $list = ProductsRepository::readCSV($filename);
                break;

            default:
                throw new Exception('Incompatible save type exception');
                break;
        }

        foreach ($list as $product) {

            if ($product->id == $productId) {

                return $product;
            }
        }
        return false;
    }
    public static function updateStock($productId, $stock)
    {

        $productList = self::getProductsList();

        foreach ($productList as $product) {

            if ($product->id == $productId) {

                $product->stock = $stock;
                $product->update($productList);
            }
        }
    }
    public function toJSON()
    {
        return json_encode($this);
    }
    public function toCSV()
    {
        return $this->id . ',' .
            $this->type . ',' .
            $this->brand . ',' .
            $this->price . ',' .
            $this->stock . ',' .
            $this->image . PHP_EOL;
    }
    public function toFilename($ext)
    {
        return '\\' . $this->type . '-' .
            $this->id . '-' .
            $this->brand . '-' .
            date("dmY_gia") . $ext;
    }
}
