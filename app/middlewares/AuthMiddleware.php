<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{

    // if (isset($_SESSION['user_name'])) {
    //     return $handler->handle($request);
    // } else {
    //     $response = new Response();
    //     $response->getBody()->write(json_encode(array("Error" => "Debes iniciar sesión")));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    // }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        var_dump($_SESSION);

        if (isset($_SESSION['user_name'])) {

            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);

            try {
                JwtAuth::VerificarToken($token);
                $response = $handler->handle($request);
            } catch (Exception $e) {
                $response = new Response();
                $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
                $response->getBody()->write($payload);
            }
            return $response->withHeader('Content-Type', 'application/json');

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("Error" => "Debes iniciar sesión")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }


    }

    public static function verificarToken(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            JwtAuth::VerificarToken($token);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');

    }
}
