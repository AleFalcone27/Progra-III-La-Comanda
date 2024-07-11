<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ProductexistsMiddleware{

    public function __invoke(Request $request, RequestHandler $handler){

        $params = $request->getParsedBody();

        if(isset($params['order_details'])){

            $products = $params['order_details'];


            foreach ($products as $product) {
                foreach ($product as $id => $quantity) {
                    if(ProductController::ProductExistsById($id)){
                        echo $id . '/n';
                        $response = new Response();
                        $response->getBody()->write(json_encode(array("Error" => "Alguno de los productos seleccionados no existen")));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
                    }
                }
            }
            return $handler->handle($request);
        }
    }
} 