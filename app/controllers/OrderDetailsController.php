<?php
use Composer\PHPStan\RuleReasonDataReturnTypeExtension;

require_once './models/OrderDetails.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class OrderDetailsController
{
    /**
     * Gets the body of the request and inserts the order Details in the db.
     */
    public static function AddDetails($request, $response, $hex)
    {
        $params = $request->getParsedBody();
        $order_hex_code = $hex;
        $details = $params['order_details'];
        
        $order_details = new OrderDetails($order_hex_code);
        
        foreach ($details as $detail) {
            foreach ($detail as $product_id => $quantity) {
                for ($i = 0; $i < $quantity; $i++) {
                        $order_details->addProduct($product_id);
                        $order_details->AddOrderDetails();
                }
            }
        }
    }


    public static function StartPrepping($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();
            $order_hex_code = $query_params['order_hex_code'];
            $product_id = $query_params['product_id'];
            $estimated_prep_time = $query_params['estimated_prep_time'];
            $user_role = GetUserRole($request);
            $user_id = GetUserID($request);

            if (OrderDetails::StartPreppingOrder($order_hex_code, $product_id, $estimated_prep_time, $user_role, $user_id)) {
                $payload = json_encode(array("message" => "Prepping Product " . $product_id . " in order: " . $order_hex_code));
            } else {
                $payload = json_encode(array("message" => "The product or the order doesn´t exist or they are already being prepared, please check the pendding orders again"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("message" => "Error atempting to start prepping " . $order_hex_code .' '. $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function EndPrepping($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();
            $order_hex_code = $query_params['order_hex_code'];
            $product_id = $query_params['id'];
            $user_role = GetUserRole($request);

            if (OrderDetails::EndPreppingOrder($order_hex_code, $product_id, $user_role)) {
                $payload = json_encode(array("message" => "End Prepping " . $product_id . " in order: " . $order_hex_code));
            } else {
                $payload = json_encode(array("message" => "The product or the order doesn´t exist or they are already being prepared, please check the pendding orders again"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("message" => "Error atempting to end prepping " . $order_hex_code .' '. $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Serve($request, $response, $args)
    {
        try {

            $query_params = $request->getQueryParams();
            $order_hex_code = $query_params['order_hex_code'];

            OrderDetails::ServerServe($order_hex_code);

            $payload = json_encode(array("message" => "Server served Sucessfully: " . $order_hex_code));

        } catch (Exception $ex) {
            $payload = json_encode(array("message" => "Error atempting to server order: " . $order_hex_code . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ReadyToServe($request, $response, $args)
    {
        try {
            $result = OrderDetails::ReadyToServerServe();

            if ($result) {

                $payload = json_encode(array("message" => $result));
            } else {

                $payload = json_encode(array("message" => "There are no orders ready to serve"));
            }

        } catch (Exception $ex) {
            $payload = json_encode(array("message" => "Error atempting to see ready to serve orders: " . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
