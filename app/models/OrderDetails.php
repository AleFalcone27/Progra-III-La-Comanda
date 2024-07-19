<?php
require_once 'utils/functions.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class OrderDetails
{
    private $order_hex_code;
    private $product_id;
    private $status;

    /*
    Status:
    0 Para Prepara
    1 En preparacion
    2 Lista para entregar
    */

    public function __construct($order_hex_code){
        $this->order_hex_code = $order_hex_code;
    }

    public function addProduct($product_id){
        $this->product_id = $product_id;
    }

    public function AddOrderDetails()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("INSERT INTO order_details (order_hex_code, product_id, status, estimated_prep_time, actual_prep_time) VALUES (:order_hex_code, :product_id, :status, :estimated_prep_time, :actual_prep_time)");
        $query->bindValue(':order_hex_code', $this->order_hex_code, PDO::PARAM_STR);
        $query->bindValue(':product_id', $this->product_id);
        $query->bindValue(':status', 0, PDO::PARAM_INT);
        $query->bindValue(':estimated_prep_time', null);
        $query->bindValue(':actual_prep_time', null);
        $query->execute();
    }


    public static function GetOrderDetails($order_hex_code)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM order_details  WHERE order_hex_code = :order_hex_code");
        $query->bindValue(":order_hex_code", $order_hex_code);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    static function StartPreppingOrder($order_hex_code, $product_id, $estimated_prep_time, $user_role, $user_id)
    {
        $time = explode(':', $estimated_prep_time);
        $date = new DateTime();
        $formated_date = $date->format('Y-m-d H:i:s');
        $formated_date = new DateTime();
        $formated_date->setTime($time[0], $time[1], $time[2]);
        $formated_date->format('Y-m-d H:i:s');
        $estimated_prep_time = $formated_date->format('Y-m-d H:i:s');

        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("UPDATE order_details
        SET status = 1,
            estimated_prep_time = :estimated_prep_time,
                prep_by = :prep_by
        WHERE status = 0 AND
            product_id IN (
                SELECT products.id
                FROM products
                WHERE products.preparation_area = :user_role
            ) AND
            order_details.product_id = :product_id AND
            order_details.order_hex_code = :order_hex_code");

        $query->bindValue(':user_role', $user_role);
        $query->bindValue(':estimated_prep_time', $estimated_prep_time);
        $query->bindValue(':product_id', $product_id);
        $query->bindValue(':order_hex_code', $order_hex_code);
        $query->bindValue(':prep_by', $user_id);
        $query->execute();
        $rowsAffected = $query->rowCount();
        return $rowsAffected > 0;
    }

    static function EndPreppingOrder($order_hex_code, $product_id, $user_role)
    {
        $date = new DateTime();
        $actual_prep_time = $date->format('Y-m-d H:i:s');

        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("UPDATE order_details
        SET status = 2,
            actual_prep_time = :actual_prep_time
        WHERE status = 1 AND
            product_id IN (
                SELECT products.id
                FROM products
                WHERE products.preparation_area = :user_role
            ) AND
            order_details.product_id = :product_id AND
            order_details.order_hex_code = :order_hex_code");

        $query->bindValue(':user_role', $user_role);
        $query->bindValue(':actual_prep_time', $actual_prep_time);
        $query->bindValue(':product_id', $product_id);
        $query->bindValue(':order_hex_code', $order_hex_code);
        $query->execute();
        $rowsAffected = $query->rowCount();
        return $rowsAffected > 0;
    }

    static function ReadyToServerServe()
    {
        //FALTA ESTA FUNCIONALIDAD PARA MATCHEAR CON UNA QUERY QUE TODOS LOS ELEMENTOS ESTEN LISTOS
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as ready_products
        FROM 
        order_details
        WHERE 
        order_hex_code = :order_hex_code;");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    static function ServerServe($order_hex_code)
    {
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




