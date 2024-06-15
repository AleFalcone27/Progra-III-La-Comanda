<?php
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class Order
{

    public $id;

    public $product_id;

    public $table_hex_code;

    public $hex_code;

    public $prep_by;

    public $status;

    public $date;

    public $estimated_prep_time;

    public $actual_prep_time;


    /**
     * Inserts a new order in the database.
     */
    public function AddOrder()
    {
        $objDataAccess = DataAccess::GetInstance();
        // $query = $objDataAccess->PrepQuery("INSERT INTO order (product_id, table_hex_code, hex_code, prep_by, status, estimated_prep_time, actual_prep_time) VALUES
            //(:product_id, :table_hex_code, :hex_code, :prep_by, :status, :estimated_prep_time, :actual_prep_time)");
        // $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        // $query->bindValue(':price', $this->price);
        // $query->bindValue(':preparation_area', $this->preparation_area);
        // $query->bindValue(':status', 1, PDO::PARAM_INT);
        // $query->bindValue(':created_at', $formatted_date);
        // $query->bindValue(':updated_at', $formatted_date);

        // $query->execute();
    }

    /**
     * Gets all the products from the database.
     * @return array returns an array containing all of the remaining rows in the result set.
     */
    // public static function GetAllProducts()
    // {
    //     $objDataAccess = DataAccess::GetInstance();
    //     $query = $objDataAccess->PrepQuery("SELECT * FROM products");
    //     $query->execute();
    //     return $query->fetchAll(PDO::FETCH_CLASS, 'Product');
    // }


    /**
     * Modifies a product by their ID from the database.
     * @param Product $product instance of user with the id to modify.
     */
    // public static function ModifyProduct($product)
    // {
    //     $objDataAccess = DataAccess::GetInstance();
    //     $query = $objDataAccess->PrepQuery('UPDATE products SET name = :name, price = :price, preparation_area = :preparation_area, updated_at = :updated_at  WHERE id = :id');
    //     $query->bindValue(':id', $product->id, PDO::PARAM_INT);
    //     $query->bindValue(':name', $product->name, PDO::PARAM_STR);
    //     $query->bindValue(':price', $product->price);
    //     $query->bindValue(':preparation_area', $product->preparation_area, PDO::PARAM_INT);
    //     $query->bindValue(':updated_at', $product->updated_at);
    //     $query->execute();
    // }

    /**
     * Changes the products´s status by their ID from the database. 
     * @param int $id from the product to be deleted. 
     */
    // public static function DeleteProduct($id)
    // {
    //     $objDataAccess = DataAccess::GetInstance();
    //     $query = $objDataAccess->PrepQuery("UPDATE users SET status = :status, updated_at = :updated_at  WHERE id = :id");
    //     $date = new DateTime();
    //     $formated_date = $date->format('Y-m-d H:i:s');
    //     $query->bindValue(':id', $id, PDO::PARAM_INT);
    //     $query->bindValue(':updated_at', $formated_date);
    //     $query->bindValue(':status', 0, PDO::PARAM_INT);
    //     $query->execute();
    // }





}