<?php
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class Product
{

    public $id;

    public $name;

    public $price;

    public $preparation_area;

    public $status;

    public $created_at;

    public $updated_at;


    /**
     * Inserts a new product in the database.
     */
    public function AddProduct()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("INSERT INTO products (name, price, preparation_area, status, created_at, updated_at) VALUES (:name, :price, :preparation_area, :status, :created_at, :updated_at)");
        $date = new DateTime();
        $formatted_date = $date->format('Y-m-d H:i:s');
        $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        $query->bindValue(':price', $this->price);
        $query->bindValue(':preparation_area', $this->preparation_area);
        $query->bindValue(':status', 1, PDO::PARAM_INT);
        $query->bindValue(':created_at', $formatted_date);
        $query->bindValue(':updated_at', $formatted_date);

        $query->execute();
    }

    /**
     * Gets all the products from the database.
     * @return array returns an array containing all of the remaining rows in the result set.
     */
    public static function GetAllProducts()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM products");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'Product');
    }


    /**
     * Modifies a product by their ID from the database.
     * @param Product $product instance of user with the id to modify.
     */
    public static function ModifyProduct($product)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery('UPDATE users SET name = :name, price = :price, preparation_area = :preparation_area, updated_at = :updated_at  WHERE id = :id');
        $query->bindValue(':id', $product->id, PDO::PARAM_INT);
        $query->bindValue(':name', $product->name, PDO::PARAM_STR);
        $query->bindValue(':price', $product->price);
        $query->bindValue(':preparation_area', $product->preparation_area, PDO::PARAM_INT);
        $query->bindValue(':updated_at', $product->updated_at);
        $query->execute();
    }






}