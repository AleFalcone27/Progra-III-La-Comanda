<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


/**
 * Middleware que verifica el rol del usuario.
 *
 * @param int $allowed_roles Roles permitidos para poder ingresar a el grupo de rutas especifico.
 */
class RoleMiddleware
{
    private $allowed_roles;
    private $user_role;

    public function __construct(...$allowed_roles)
    { 
        $this->allowed_roles = $allowed_roles;
        if(isset($_SESSION["user_role"])){
            $this->user_role = $_SESSION["user_role"];
        }
    }

    public function __invoke(Request $request, RequestHandler $handler){

        if(in_array($this->user_role,$this->allowed_roles)){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $response->getBody()->write(json_encode(array("Error" => "No tienes permisos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401); 
        }

       return $handler->handle($request);
    } 

}