<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class LoginMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $params = $request->getParsedBody();

        $user_name = $params['user_name'];

        $data = array('usuario' => $user_name);


        $token = JwtAuth::CrearToken($data);

        JwtAuth::VerificarToken($token);

        $payload = json_encode(array("jwt" => $token));

        echo $payload;

        $response = $handler->handle($request);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}

