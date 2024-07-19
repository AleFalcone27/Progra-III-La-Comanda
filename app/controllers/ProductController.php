<?php
require_once './models/Product.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class ProductController
{
    /**
     * Gets the body of the request and inserts a new Product in the db.
     * @return response 
     */
    public function AddOne($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            if (!in_array($params['preparation_area'], UserController::$VALID_AREA)) {
                throw new Exception;
            } else {
                $product = new Product($params['name'],$params['price'],$params['preparation_area']);
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
            $product = new Product($query_params['name'],$query_params['price'],$query_params['preparation_area']);
            Product::ModifyProduct($product, $id);

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

    public function PopulateByCSV($request, $response, $args){

        try{
            $args = $request->getParsedBody();
            $file_name = $args['file_name'];
            Product::Populate($file_name);
            $payload = json_encode(array("message" => "Table Products populated "));
        }
        catch (Exception $ex){
            $payload = json_encode(array("message" => "Error atempting to populate Products table ". $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ProductExistsById($product_id){
        try {
            if(Product::GetProductById($product_id)){
                return true;
            }else return false; 
        }
        catch (Exception $ex){
        }
    }

    public static function GetProductsCSV($request, $response, $args){
        try{
            Product::GetByCSV();
            $payload = json_encode(array("message" => "File Created Succesfully"));
        }
        catch (Exception $ex){
            $payload = json_encode(array("message" => "Unable to get products from database ". $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
