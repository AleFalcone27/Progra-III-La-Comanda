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
                if(!ProductController::ProductExistsById(array_values($product)[0])){
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("Error" => "El producto seleccionado no existe o no hay stock")));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
                }
            }
            return $handler->handle($request);
        }
    }
} 