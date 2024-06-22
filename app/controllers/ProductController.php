<?php
require_once './models/Product.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class ProductController extends Product implements IApiUsable
{
    /**
     * Gets the body of the request and inserts a new Product in the db.
     * @return response 
     */
    public function AddOne($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $name = $params['name'];
            $price = $params['price'];
            $preparation_area = $params['preparation_area'];

            if (!in_array($preparation_area, UserController::$VALID_AREA)) {
                throw new Exception;
            } else {
                $product = new Product();
                $product->name = $name;
                $product->price = $price;
                $product->preparation_area = $preparation_area;
                $product->AddProduct();
                
                $payload = json_encode(array("Message" => "Product created Sucessfully"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to create new Product ". $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /**
     * Gets the requests args and gets a user by their ID from the database.
     * @return response 
     */
    public function GetOne($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $user = $args['name'];
        $usuario = User::GetOneUser($user);
        $payload = json_encode($usuario);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets all the products from the database.
     * @return response.
     */
    public function GetAll($request, $response, $args)
    {
        try {
            $list = Product::GetAllProducts();
            $payload = json_encode(array("Products:" => $list));
        } catch (Exception $ex) {
            $payload = json_encode(array("Message:" => 'Error trying to get all products: '. $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets the query paarams from de request and modifies a Product by their ID from the database.
     * @return response 
     */
    public function ModifyOne($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();

            $id = $query_params['id'];
            $name = $query_params['name'];
            $price = $query_params['price'];
            $preparation_area = $query_params['preparation_area'];

            $date = new DateTime();
            $formated_date = $date->format('Y-m-d H:i:s');
            $product = new Product();
            $product->id = $id;
            $product->name = $name;
            $product->price = $price;
            $product->preparation_area = $preparation_area;
            $product->updated_at = $formated_date;

            Product::ModifyProduct($product);

            $payload = json_encode(array("Message" => "Product successfully modified"));
        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to modify product ". $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets the body  from de request and changes the products´s status by their ID from the database.
     * @return response 
     */
    public function DeleteOne($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();
            $id = $query_params['id'];

            Product::DeleteProduct($id);

            $payload = json_encode(array("message" => "Product deleted succesfully"));
        } catch (Exception $ex) {

            $payload = json_encode(array("message" => "Error atempting to delete product ". $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ProductExistsById($product_id){
        try{
            if(Product::GetProductById($product_id)){
                return true;
            }else return false; 
        }
        catch (Exception $ex){
        }
        
    }
}
