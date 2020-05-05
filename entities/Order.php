<?php
require_once __DIR__ . '\..\repos\OrdersRepository.php';

class Order
{
    public $id;
    public $userId;
    public $date;
    public $orderItems;
    public $totalAmount;

    public function getTotal()
    {
        $total = 0;

        foreach ($this->orderItems as $item) {

            $total += ($item->qty * $item->productPrice);
        }
        return $total;
    }
    public function __construct($userId, $orderItems, $orderId = null)
    {

        $this->userId = $userId;
        $this->id = ($orderId ?? strtotime('now'));
        $this->date = date("dmY_gia");;
        $this->orderItems = $orderItems;
        $this->totalAmount = $this->getTotal();
    }
    public function save()
    {

        $filename = getenv('ORDERS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch ($ext) {
            case 'TXT':
                OrdersRepository::saveSerialized($filename, $this);
                break;

            case 'JSON':
                OrdersRepository::saveJSON($filename, $this->toJSON());
                break;

            case 'CSV':
                OrdersRepository::saveCSV($filename, $this->toCSV());
                break;

            default:
                throw new Exception('Incompatible save type exception');
                break;
        }
    }
    public static function getOrderList()
    {
        $filename = getenv('ORDERS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch ($ext) {
            case 'TXT':
                $list = OrdersRepository::readSerialized($filename);
                break;

            case 'JSON':
                $list = OrdersRepository::readJSON($filename);
                break;

            case 'CSV':
                $list = OrdersRepository::readCSV($filename);
                break;

            default:
                throw new Exception('Incompatible save type exception');
                break;
        }

        return $list;
    }
    public static function getOrdersByUserType($userContext)
    {
        $list = array();

        if ($userContext->role == 'admin') {
            $list = self::getOrderList();
        }

        if ($userContext->role == 'user') {
            $list = self::fetchOrdersByUserId($userContext->userId);
        }

        return $list;
    }
    public static function fetchOrdersByUserId($userId)
    {
        $orderList = Order::getOrderList();

        $filteredList = array();

        foreach ($orderList as $order) {

            if ($order->userId == $userId) {

                array_push($filteredList, $order);
            }
        }
        return $filteredList;
    }
    public function toJSON()
    {
        return json_encode($this);
    }
    public function toCSV()
    {
        return $this->id . ',' .
            $this->date . ',' .
            $this->orderItems . ',' .
            $this->status . ',' .
            $this->totalAmount . PHP_EOL;
    }
}

class OrderItem
{
    public $productId;
    public $productPrice;
    public $qty;

    public function __construct($productId, $productPrice, $qty)
    {
        $this->productId = $productId;
        $this->productPrice = (float) $productPrice;
        $this->qty = $qty;
    }
}