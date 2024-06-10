<?php
require_once './models/User.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class UserController extends User implements IApiUsable
{
  public static $VALID_AREA = array(1, 2, 3, 4);

  /**
   * Gets the body of the request and inserts a new User in the db.
   * @return response 
   */
  public function AddOne($request, $response, $args)
  {
    try {
      $params = $request->getParsedBody();
      $name = $params['name'];
      $password = $params['password'];
      $role = $params['role'];

      if (!in_array($role, self::$VALID_AREA)) {
        throw new Exception;
      } else {
        $user = new User();
        $user->name = $name;
        $user->password = $password;
        $user->role = $role;

        $user->AddUser();
        $payload = json_encode(array("Message" => "User created Sucessfully"));
      }
    } catch (Exception $ex) {
      $payload = json_encode(array("mensaje" => "Error atempting to create new User"));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  /**
   * Gets the requests args and gets a user by their ID from the database.
   * @return response 
   */
  public function GetOne($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $user = $args['name'];
    $usuario = User::GetOneUser($user);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Gets all the users from the database.
   * @return response.
   */
  public function GetAll($request, $response, $args)
  {
    try {
      $list = User::GetAllUsers();
      $payload = json_encode(array("Users:" => $list));
    } catch (Exception $ex) {
      $payload = json_encode(array("Message:" => 'Error trying to get all users'));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Gets the query paarams from de request and modifies a user by their ID from the database.
   * @return response 
   */
  public function ModifyOne($request, $response, $args)
  {
    try {
      $query_params = $request->getQueryParams();

      $id = $query_params['id'];
      $name = $query_params['name'];
      $password = $query_params['password'];
      $role = $query_params['role'];

      $date = new DateTime();
      $formated_date = $date->format('Y-m-d H:i:s');
      $user = new User();
      $user->id = $id;
      $user->name = $name;
      $user->password = $password;
      $user->role = $role;
      $user->updated_at = $formated_date;

      User::ModifyUser($user);

      $payload = json_encode(array("Message" => "User successfully modified"));
    } catch (Exception $ex) {
      $payload = json_encode(array("Message" => "Error atempting to modify user"));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Gets the body  from de request and changes the user´s status by their ID from the database.
   * @return response 
   */
  public function DeleteOne($request, $response, $args)
  {
    try {
      $params = $request->getParsedBody();
      $user_id = $params['id'];
      
      User::DeleteUser($user_id);

      $payload = json_encode(array("message" => "User deleted succesfully"));
    } catch (Exception $ex) {

      $payload = json_encode(array("message" => "Error atempting to delete user"));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
