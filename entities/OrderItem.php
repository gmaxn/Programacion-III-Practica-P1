<?php

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
