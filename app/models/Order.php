<?php
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use function Symfony\Component\Clock\now;

require_once 'utils/functions.php';

class Order
{
    public $id;
    public $table_hex_code;
    public $hex_code;
    public $date;
    public $status;
    public $estimated_prep_time;
    public $actual_prep_time;

    // me falta agregar la funcionalidad de la foto cuando se toma una orden

    /**
     * Inserts a new order in the database.
     */
    public function AddOrder()
    {
        $date = new DateTime();
        $formated_date = $date->format('Y-m-d H:i:s');
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("INSERT INTO orders (hex_code, table_hex_code, status, date) VALUES (:hex_code, :table_hex_code, :status, :date)");
        $query->bindValue(':table_hex_code', $this->table_hex_code, PDO::PARAM_STR);
        $query->bindValue(':hex_code', $this->hex_code, PDO::PARAM_STR);
        $query->bindValue(':date', $formated_date);
        $query->bindValue(':status', 0, PDO::PARAM_INT);

        $query->execute();
    }

    /**
     * Gets all the orders from the database.
     * @return array returns an array containing all of the remaining rows in the result set.
     */
    public static function GetOrdersToPrepare($user_role)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT order_details.id,name,order_hex_code FROM order_details
        JOIN products ON product_id = products.id
        WHERE order_details.status = 0 AND products.preparation_area = :user_role
        ");
        $query->bindValue(':user_role', $user_role);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Changes the orders´s status by their hex_code from the database. 
     * @param int $id from the order to be deleted. 
     */
    public function UpdateStatus()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("UPDATE orders SET status = :status, WHERE hex_code = :hex_code");
        $date = new DateTime();
        $query->bindValue(':hex_code', $this->hex_code, PDO::PARAM_STR);
        $query->bindValue(':status', $this->status, PDO::PARAM_INT);
        $query->execute();
    }

    public static function GetOrder($hex_code)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM orders where hex_code = :hex_code");
        $query->bindParam(":hex_code", $hex_code);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Modifies an order by their ID from the database.
     * @param Order $order instance of user with the id to modify.
     */
    public static function ModifyOrder($order)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery('UPDATE orders SET hex_code = :hex_code, table_hex_code = :table_hex_code, date = :date, status = :status,  WHERE id = :id');
        $query->bindValue(':id', $order->id, PDO::PARAM_INT);
        $query->bindValue(':hex_code', $order->hex_code);
        $query->bindValue(':table_hex_code', $order->table_hex_code);
        $query->bindValue(':status', $order->status, PDO::PARAM_INT);
        $query->bindValue(':date', $order->date);
        $query->execute();
    }


    public static function ToCSVFile($location){
        try{
            $data_access_obj = DataAccess::GetInstance();
            $query = $data_access_obj->prepQuery("SELECT * FROM orders");
            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) {
            throw new Exception('The table is empty or does not exists' );
        }else{
            return $rows;
        }
    } catch (Exception $e) {
        echo $e;
        return array();
    }
    }

public static function GetOrdersByTableHexCode($hex_code)
{
    try {
        $hex_code = trim(strtolower($hex_code));
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM orders WHERE table_hex_code = :hex_code");
        $query->bindParam(":hex_code", $hex_code);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            throw new Exception("No orders found for this table.");
        }

        return $rows;
    } catch (Exception $e) {
        echo $e->getMessage();
        return array();
    }
}

public static function GetEstimatedPrepTimeGroupedByOrder()
{
    try {
        $objDataAccess = DataAccess::GetInstance();

        $query = $objDataAccess->PrepQuery("
            SELECT order_hex_code, SUM(estimated_prep_time) AS total_estimated_prep_time
            FROM order_details
            GROUP BY order_hex_code
        ");

        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            throw new Exception("No orders found.");
        }

        $result = [];

         foreach ($rows as $row) {
            $start = new DateTime($row['created_at']);
            $end = new DateTime($row['estimated_prep_time']);

            // Calcular la diferencia de tiempo
            $interval = $start->diff($end);

            $hours = $interval->h + $interval->d * 24; // Por si hay días de diferencia
            $minutes = $interval->i;

            $formattedTime = sprintf("%dh %dm", $hours, $minutes);

            $result[] = [
                'order_hex_code' => $row['order_hex_code'],
                'estimated_time_formatted' => $formattedTime,
            ];
        }                                                                                                                                                                                     
        return $result;

    } catch (Exception $e) {
        echo $e->getMessage();
        return [];
    }
}
}