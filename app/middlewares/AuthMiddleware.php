<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        if (isset($_SESSION['user_name'])) {
            return $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("Error" => "Debes iniciar sesión")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}
