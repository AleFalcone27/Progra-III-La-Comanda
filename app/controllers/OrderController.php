<?php
require_once './models/Order.php';
require_once 'Controllers/OrderDetailsController.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class OrderController extends Product implements IApiUsable
{
    /**
     * Gets the body of the request and inserts a new Order in the db.
     * @return response 
     */
    public function AddOne($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $hex_code = $params['hex_code'];
            $table_hex_code = $params['table_hex_code'];
            $estimated_prep_time = $params['estimated_prep_time'];

            $order = new Order();
            $order->hex_code = $hex_code;
            $order->table_hex_code = $table_hex_code;
            $order->estimated_prep_time = $estimated_prep_time;
            $order->AddOrder();

            OrderDetailsController::AddDetails($request,$response,$args);

            $payload = json_encode(array("Message" => "Order created Sucessfully"));

        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to create new Order " . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /**
     * Gets the requests args and gets an order by their hex_code from the database.
     * @return response 
     */
    public function GetOne($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $hex_code = $args['hex_code'];
        $order = Order::GetOrder($hex_code);
        $payload = json_encode($order);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets all the Orders from the database.
     * @return response.
     */
    public function GetAll($request, $response, $args)
    {
        try {
            $list = Order::GetAllOrdes();
            $payload = json_encode(array("Orders:" => $list));
        } catch (Exception $ex) {
            $payload = json_encode(array("Message:" => 'Error trying to get all orders: ' . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets the query paarams from de request and modifies a Product by their ID from the database.
     * @return response 
     */
    public function UpdateOne($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();

            $hex_code = $query_params['hex_code'];
            $status = $query_params['status'];

            $order = new Order();
            $order->hex_code = $hex_code;
            $order->status = $status;

            $order->UpdateStatus();

            $payload = json_encode(array("Message" => "Product successfully modified"));
        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to modify product " . $ex->getMessage()));
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

            $payload = json_encode(array("message" => "Error atempting to delete product " . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModifyOne($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();

            $id = $query_params['id'];
            $hex_code = $query_params['hex_code'];
            $table_hex_code = $query_params['table_hex_code'];
            $estimated_prep_time = $query_params['estimated_prep_time'];
            $date = $query_params['date'];
            $status = $query_params['status'];
            $actual_prep_time = $query_params['actual_prep_time'];
            
            $order = new Order();
            $order->id = $id;
            $order->hex_code = $hex_code;
            $order->table_hex_code = $table_hex_code;
            $order->estimated_prep_time = $estimated_prep_time;
            $order->date = $date;
            $order->status = $status;
            $order->actual_prep_time = $actual_prep_time;

            Order::ModifyOrder($order);

            $payload = json_encode(array("Message" => "ORder successfully modified"));
        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to modify order " . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
