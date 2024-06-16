<?php
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class Order
{
    public $id;

    public $hex_code;

    public $table_hex_code;
    public $estimated_prep_time;

    public $status;

    public $date;

    public $actual_prep_time;


    /**
     * Inserts a new order in the database.
     */
    public function AddOrder()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("INSERT INTO orders (table_hex_code, hex_code, date, status, estimated_prep_time, actual_prep_time) VALUES (:table_hex_code, :hex_code, :status, :date , :estimated_prep_time, :actual_prep_time)");

        $date = new DateTime();
        $formated_date = $date->format('Y-m-d H:i:s');
        $query->bindValue(':table_hex_code', $this->table_hex_code, PDO::PARAM_STR);
        $query->bindValue(':hex_code', $this->hex_code, PDO::PARAM_STR);
        $query->bindValue(':date', $formated_date);
        $query->bindValue(':status', 1, PDO::PARAM_INT);
        $query->bindValue(':estimated_prep_time', $this->estimated_prep_time);
        $query->bindValue(':actual_prep_time', null);

        $query->execute();
    }

    /**
     * Gets all the orders from the database.
     * @return array returns an array containing all of the remaining rows in the result set.
     */
    public static function GetAllOrdes()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM orders");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'Order');
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

// (table_hex_code, hex_code, date, status, estimated_prep_time, actual_prep_time) VALUES (:table_hex_code, :hex_code, :status, :date , :estimated_prep_time, :actual_prep_time)");


    /**
     * Modifies an order by their ID from the database.
     * @param Order $order instance of user with the id to modify.
     */
    public static function ModifyOrder($order)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery('UPDATE orders SET hex_code = :hex_code, table_hex_code = :table_hex_code, date = :date, status = :status, estimated_prep_time = :estimated_prep_time, actual_prep_time = :actual_prep_time  WHERE id = :id');
        $query->bindValue(':id', $order->id, PDO::PARAM_INT);
        $query->bindValue(':hex_code', $order->hex_code);
        $query->bindValue(':table_hex_code', $order->table_hex_code);
        $query->bindValue(':status', $order->status, PDO::PARAM_INT);
        $query->bindValue(':date', $order->date);
        $query->bindValue(':estimated_prep_time', $order->estimated_prep_time);
        $query->bindValue(':actual_prep_time', $order->actual_prep_time);
        $query->execute();
    }

}