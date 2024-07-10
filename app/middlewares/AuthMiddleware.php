<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    private $allowed_roles;

    public function __construct(...$allowed_roles)
    {
        $this->allowed_roles = $allowed_roles;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        $header = $request->getHeaderLine('Authorization');
        if (strpos($header, 'Bearer') !== false) {
            $parts = explode("Bearer", $header);

            $token = trim($parts[1]);

            JwtAuth::VerificarToken($token);

            $user_role = JwtAuth::ObtenerData($token)[1];

            if (in_array($user_role, $this->allowed_roles)) {
                $response = $handler->handle($request);
                return $response;
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(["message" => "You are not Autorized"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        } else {
            throw new Exception("The Token is missing");
        }


    }
}


