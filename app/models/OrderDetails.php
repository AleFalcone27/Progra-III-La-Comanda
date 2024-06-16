<?php

class OrderDetails{
    public $order_hex_code;
    public $product_id;
    public $quantity;
    public $status;
    public $estimated_prep_time;
    public $actual_prep_time;

    /*
    Status:
    0 Para Prepara
    1 En preparacion
    2 Lista para entregar
    */

    public function AddOrderDetails()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("INSERT INTO order_details (order_hex_code, product_id, quantity, status, estimated_prep_time, actual_prep_time) VALUES (:order_hex_code, :product_id, :quantity, :status, :estimated_prep_time, :actual_prep_time)");
        $query->bindValue(':order_hex_code', $this->order_hex_code, PDO::PARAM_STR);
        $query->bindValue(':product_id', $this->product_id);
        $query->bindValue(':quantity', $this->quantity);
        $query->bindValue(':status', 0, PDO::PARAM_INT);
        $query->bindValue(':estimated_prep_time', $this->estimated_prep_time);
        $query->bindValue(':actual_prep_time', null);
        $query->execute();
    }

    // Escribir metodo para actualiazar el estado a en preparacion 

    // Escribir metodo para actualiazar el estado a Listo para entregar 


}



