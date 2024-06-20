<?php

class OrderDetails {
    public $order_hex_code;
    public $product_id;
    public $quantity;
    public $status;

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
        $query->bindValue(':estimated_prep_time', null);
        $query->bindValue(':actual_prep_time', null);
        $query->execute();
    }


    public static function GetOrderDetails($order_hex_code) {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM order_details  WHERE order_hex_code = :order_hex_code");
        $query->bindValue(":order_hex_code", $order_hex_code);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    // puedo agregar un middleware para verificar si realmente esta orden existe y si su estado es en no preparacion
    static function StartPreppingOrder($order_hex_code, $user_id, $estimated_prep_time){

        $flag = false;

        $user_role = User::GetUserRoleById($user_id);
        $prep_areas = Product::GetPreparationArea($order_hex_code);
        $orderDetails = OrderDetails::GetOrderDetails($order_hex_code);
    
        foreach ($orderDetails as $orderDetail) {
            $product_id = $orderDetail['product_id'];
            $product_prep_area = $prep_areas[$product_id];
    
            if ($product_prep_area == $user_role) {
                $flag = true;
                $objDataAccess = DataAccess::GetInstance();
                $query = $objDataAccess->PrepQuery("UPDATE order_details SET status = :status, estimated_prep_time = :estimated_prep_time, prep_by = :prep_by  WHERE order_hex_code = :order_hex_code AND product_id = :product_id");
                $query->bindValue(":status", 1 , PDO::PARAM_INT);
                $query->bindValue(":estimated_prep_time", $estimated_prep_time);
                $query->bindValue(":prep_by", $user_id, PDO::PARAM_INT);
                $query->bindValue(":order_hex_code", $order_hex_code);  
                $query->bindValue(":product_id", $product_id, PDO::PARAM_INT);
                $query->execute(); 
            }
        }

        if (!$flag) {
            throw new Exception("El usuario no tiene nada para preparar en esta orden");
        }
    }
    
    // puedo agregar un middleware para verificar si realmente esta orden existe y si su estado es en no preparacion
    static function EndPreppingOrder($order_hex_code, $user_id, $actual_prep_time){

        $flag = false;

        $user_role = User::GetUserRoleById($user_id);
        $prep_areas = Product::GetPreparationArea($order_hex_code);
        $orderDetails = OrderDetails::GetOrderDetails($order_hex_code);
    
        foreach ($orderDetails as $orderDetail) {
            $product_id = $orderDetail['product_id'];
            $product_prep_area = $prep_areas[$product_id];
    
            if ($product_prep_area == $user_role) {
                $flag = true;
                $objDataAccess = DataAccess::GetInstance();
                $query = $objDataAccess->PrepQuery(" UPDATE order_details SET status = :status, actual_prep_time = :actual_prep_time WHERE order_hex_code = :order_hex_code AND product_id = :product_id");
                $query->bindValue(":status", 2 , PDO::PARAM_INT);
                $query->bindValue(":actual_prep_time", $actual_prep_time);
                $query->bindValue(":order_hex_code", $order_hex_code);  
                $query->bindValue(":product_id", $product_id, PDO::PARAM_INT);
                $query->execute(); 
            }
        }

        if (!$flag) {
            throw new Exception("El usuario no esta preparando nada de esta orden...");
        }
    }


    static function ServerServe($order_hex_code) {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT status FROM order_details WHERE order_hex_code = :order_hex_code");
        $query->bindValue(":order_hex_code", $order_hex_code, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($results as $row) {
            if ($row['status'] != 2) {
                throw new Exception(' La orden aún no está lista');
            }
        }
        
        $query_update = $objDataAccess->PrepQuery("UPDATE order_details SET status = 3 WHERE order_hex_code = :order_hex_code");
        $query_update->bindValue(":order_hex_code", $order_hex_code, PDO::PARAM_STR);
        $query_update->execute();
    }
    
}




