<?php
require_once './models/OrderDetails.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class OrderDetailsController extends OrderDetails 
{
    /**
     * Gets the body of the request and inserts the order Details in the db.
     * @return response 
     */
    public static function AddDetails($request, $response, $args)
    {
        try {    
            $params = $request->getParsedBody();
            $order_hex_code = $params['hex_code'];
            $details = $params['order_details'];

            $order_details = new OrderDetails();
            $order_details->order_hex_code = $order_hex_code;

            foreach ($details as $detail) {
                foreach ($detail as $product_id => $quantity) {
                    $order_details->product_id = $product_id;
                    $order_details->quantity = $quantity;
                    $order_details->AddOrderDetails();
                }
            }

            $payload = json_encode(array("Message" => "Order details created Sucessfully"));

        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to add details to an order " . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function StartPrepping($request, $response, $args){
        try {

            $query_params = $request->getQueryParams();
            $order_hex_code = $query_params['order_hex_code'];
            $user = $query_params['user_id'];
            $estimated_prep_time = $query_params['estimated_prep_time'];

            OrderDetails::StartPreppingOrder($order_hex_code,$user,$estimated_prep_time);

            $payload = json_encode(array("Message" => "Prepping Order " . $order_hex_code ));
            

        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to start prepping " . $order_hex_code . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function EndPrepping($request, $response, $args){
        try {

            $query_params = $request->getQueryParams();
            $order_hex_code = $query_params['order_hex_code'];
            $user = $query_params['user_id'];
            $actual_prep_time = $query_params['actual_prep_time'];

            OrderDetails::EndPreppingOrder($order_hex_code,$user,$actual_prep_time);

            $payload = json_encode(array("Message" => "End prepping Order " . $order_hex_code ));
            

        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to end prepping " . $order_hex_code . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Serve($request, $response, $args){
        try {

            $query_params = $request->getQueryParams();
            $order_hex_code = $query_params['order_hex_code'];

            OrderDetails::ServerServe($order_hex_code);

            $payload = json_encode(array("Message" => "Server served Sucessfully: " . $order_hex_code ));
            
        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to server order: " . $order_hex_code . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    // /**
    //  * Gets the requests args and gets an order by their ID from the database.
    //  * @return response 
    //  */
    // public function GetOne($request, $response, $args)
    // {
    //     // Buscamos usuario por nombre
    //     $user = $args['name'];
    //     $usuario = User::GetOneUser($user);
    //     $payload = json_encode($usuario);
    //     $response->getBody()->write($payload);
    //     return $response->withHeader('Content-Type', 'application/json');
    // }

    // /**
    //  * Gets all the Orders from the database.
    //  * @return response.
    //  */
    // public function GetAll($request, $response, $args)
    // {
    //     try {
    //         $list = Order::GetAllOrdes();
    //         $payload = json_encode(array("Orders:" => $list));
    //     } catch (Exception $ex) {
    //         $payload = json_encode(array("Message:" => 'Error trying to get all orders: ' . $ex->getMessage()));
    //     }
    //     $response->getBody()->write($payload);
    //     return $response->withHeader('Content-Type', 'application/json');
    // }

    // /**
    //  * Gets the query paarams from de request and modifies a Product by their ID from the database.
    //  * @return response 
    //  */
    // public function UpdateOne($request, $response, $args)
    // {
    //     try {
    //         $query_params = $request->getQueryParams();

    //         $hex_code = $query_params['hex_code'];
    //         $status = $query_params['status'];

    //         $order = new Order();
    //         $order->hex_code = $hex_code;
    //         $order->status = $status;

    //         $order->UpdateStatus();

    //         $payload = json_encode(array("Message" => "Product successfully modified"));
    //     } catch (Exception $ex) {
    //         $payload = json_encode(array("Message" => "Error atempting to modify product " . $ex->getMessage()));
    //     }
    //     $response->getBody()->write($payload);
    //     return $response->withHeader('Content-Type', 'application/json');
    // }

    // /**
    //  * Gets the body  from de request and changes the products´s status by their ID from the database.
    //  * @return response 
    //  */
    // public function DeleteOne($request, $response, $args)
    // {
    //     try {
    //         $query_params = $request->getQueryParams();
    //         $id = $query_params['id'];

    //         Product::DeleteProduct($id);

    //         $payload = json_encode(array("message" => "Product deleted succesfully"));
    //     } catch (Exception $ex) {

    //         $payload = json_encode(array("message" => "Error atempting to delete product " . $ex->getMessage()));
    //     }
    //     $response->getBody()->write($payload);
    //     return $response->withHeader('Content-Type', 'application/json');
    // }

    // public function ModifyOne($request, $response, $args)
    // {
    //     try {
    //         $query_params = $request->getQueryParams();

    //         $id = $query_params['id'];
    //         $hex_code = $query_params['hex_code'];
    //         $table_hex_code = $query_params['table_hex_code'];
    //         $estimated_prep_time = $query_params['estimated_prep_time'];
    //         $date = $query_params['date'];
    //         $status = $query_params['status'];
    //         $actual_prep_time = $query_params['actual_prep_time'];
            
    //         $order = new Order();
    //         $order->id = $id;
    //         $order->hex_code = $hex_code;
    //         $order->table_hex_code = $table_hex_code;
    //         $order->estimated_prep_time = $estimated_prep_time;
    //         $order->date = $date;
    //         $order->status = $status;
    //         $order->actual_prep_time = $actual_prep_time;

    //         Order::ModifyOrder($order);

    //         $payload = json_encode(array("Message" => "ORder successfully modified"));
    //     } catch (Exception $ex) {
    //         $payload = json_encode(array("Message" => "Error atempting to modify order " . $ex->getMessage()));
    //     }
    //     $response->getBody()->write($payload);
    //     return $response->withHeader('Content-Type', 'application/json');
    // }

}
