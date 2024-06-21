<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        if(!empty($_SESSION)){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $response->getBody()->write(json_encode(array("Error" => "Debes iniciar iniciar sesion")));
        }
        return $response;
    }
}
