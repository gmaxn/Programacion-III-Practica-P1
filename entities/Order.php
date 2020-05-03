<?php

require_once __DIR__ . '\..\repos\OrdersRepository.php';

class Order {
    
    public $id;
    public $userId;
    public $date;
    public $orderItems;
    public $totalAmount;

    public function getTotal() {

        $total = 0;

        foreach($this->orderItems as $item) {

            $total += ($item->qty * $item->productPrice);
        }
        return $total;
    }

    public function __construct($userId, $orderItems, $orderId = null) {

        $this->userId = $userId;
        $this->id = ($orderId ?? strtotime('now'));
        $this->date = date("dmY_gia");;
        $this->orderItems = $orderItems;
        $this->totalAmount = $this->getTotal();
    }

    public function save() {

        $filename = getenv('ORDERS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch($ext)
        {
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
    
    public function toJSON() {
        
        return json_encode($this);
    }
    public function toCSV() {

        return $this->id . ',' . 
               $this->date . ',' . 
               $this->orderItems . ',' . 
               $this->status . ',' . 
               $this->totalAmount . PHP_EOL; 
    }
}