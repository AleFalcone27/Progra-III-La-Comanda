<?php
use Symfony\Component\Console\Event\ConsoleCommandEvent;

date_default_timezone_set('America/Argentina/Buenos_Aires');

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
        $query = $objDataAccess->PrepQuery('UPDATE products SET name = :name, price = :price, preparation_area = :preparation_area, updated_at = :updated_at  WHERE id = :id');
        $query->bindValue(':id', $product->id, PDO::PARAM_INT);
        $query->bindValue(':name', $product->name, PDO::PARAM_STR);
        $query->bindValue(':price', $product->price);
        $query->bindValue(':preparation_area', $product->preparation_area, PDO::PARAM_INT);
        $query->bindValue(':updated_at', $product->updated_at);
        $query->execute();
    }

    /**
     * Changes the products´s status by their ID from the database. 
     * @param int $id from the product to be deleted. 
     */
    public static function DeleteProduct($id)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("UPDATE users SET status = :status, updated_at = :updated_at  WHERE id = :id");
        $date = new DateTime();
        $formated_date = $date->format('Y-m-d H:i:s');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':updated_at', $formated_date);
        $query->bindValue(':status', 0, PDO::PARAM_INT);
        $query->execute();
    }


    public static function GetPreparationArea($order_hex_code)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT p.preparation_area FROM products p JOIN order_details od ON p.id = od.product_id WHERE od.order_hex_code = :order_hex_code
        ");
        $query->bindValue(':order_hex_code', $order_hex_code, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $preparation_area = array_column($results, 'preparation_area');
        return $preparation_area;
    }

    public static function GetProductById($product_id)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM products WHERE id = :id AND status = :status");
        $query->bindValue(':id', $product_id, PDO::PARAM_INT);
        $query->bindValue(':status', 1, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function Populate($file_name)
    {
        try {
            $path = '../app/UploadedProducts/';

            $full_path = $path . $file_name;

            $csv = fopen($full_path, 'r');

            if ($csv === false) {
                throw new Exception("No se pudo abrir el archivo CSV.");
            }

            while (($row = fgetcsv($csv)) !== false) {
                $p = new Product();
                $p->name = $row[0];
                $p->price = $row[1];
                $p->preparation_area = $row[2];
                $p->AddProduct();
            }

            fclose($csv); 

        } catch (Exception $ex) {
            echo "Error: " . $ex->getMessage();
        }

    }
}