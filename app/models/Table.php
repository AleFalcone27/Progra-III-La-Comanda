<?php
class Table
{
    public $id;
    public $hex_code;
    public $customer_count;
    public $status;


    public function __construct($customer_count, $status = 1){
        $this->customer_count = $customer_count;
        $this->status = $status;
    }

    /**
     * Inserts a new table in the database.
     */
    public function AddTable() 
    {
        $bytes = random_bytes(ceil(5 / 2));
        $hex_code = substr(bin2hex($bytes), 0, 5);
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("INSERT INTO tables (hex_code, customer_count, status) VALUES (:hex_code, :customer_count, :status )");
        $query->bindValue(':hex_code', $hex_code, PDO::PARAM_STR );
        $query->bindValue(':customer_count', $this->customer_count, PDO::PARAM_INT );
        $query->bindValue(':status', $this->status, PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * Gets all the tables from the database.
     * @return array returns an array containing all of the remaining rows in the result set.
     */
    public static function GetAllTables()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM tables");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'Table');
    }

    /**
     * Gets a table by their ID from the database.
     * @param user $user instance of user with the id to get.
     * @return array|false Returns an instance of the required class with property names that correspond to the column names or false on failure.
     */
    public static function GetOneTable($hex_code)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM tables WHERE hex_code = :hex_code");
        $query->bindValue(':hex_code', $hex_code, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchObject('table');
    }
    /**
     * Modifies a table by their ID from the database.
     * @param table $table instance of user with the id to modify.
     */
    public static function ModifyTable($table,$hex_code)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery('UPDATE tables SET customer_count = :customer_count, status = :status WHERE hex_code = :hex_code');
        $query->bindValue(':hex_code', $table->hex_code, PDO::PARAM_INT);
        $query->bindValue(':customer_count', $table->customer_count, PDO::PARAM_INT);
        $query->bindValue(':status', $table->status, PDO::PARAM_INT);
        $query->execute();
    }

    public static function DeleteTable($id)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery('UPDATE tables SET status = :status WHERE id = :id');
        $query->bindValue(':status', 0, PDO::PARAM_INT);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

}