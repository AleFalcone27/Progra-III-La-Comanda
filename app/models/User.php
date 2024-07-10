<?php
class User
{
    public $id;
    public $name;
    public $password;
    public $status;

    public $role;
    public $created_at;

    public $updated_at;

    /**
     * Inserts a new user in the database.
     */
    public function AddUser() 
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("INSERT INTO users (name, password, role, status, created_at, updated_at) VALUES (:name, :password, :role, :status ,:created_at, :updated_at)");
        $date = new DateTime();
        $formated_date = $date->format('Y-m-d H:i:s');
        $HashedPass = password_hash($this->password, PASSWORD_DEFAULT);
        $query->bindValue(':name', $this->name, PDO::PARAM_STR);
        $query->bindValue(':password', $HashedPass);
        $query->bindValue(':role', $this->role);
        $query->bindValue(':status', 1, PDO::PARAM_INT);
        $query->bindValue(':created_at', $formated_date);
        $query->bindValue(':updated_at', $formated_date);
        $query->execute();
    }

    /**
     * Gets all the users from the database.
     * @return array returns an array containing all of the remaining rows in the result set.
     */
    public static function GetAllUsers()
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM users");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'User');
    }

    /**
     * Gets a user by their ID from the database.
     * @param user $user instance of user with the id to get.
     * @return array|false Returns an instance of the required class with property names that correspond to the column names or false on failure.
     */
    public static function GetOneUser($user)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT id, name, password, role, created_at, updated_at FROM users WHERE name = :name");
        $query->bindValue(':name', $user, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchObject('user');
    }

    public static function GetUserRoleById($user_id){
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT role FROM users WHERE id = :id");
        $query->bindValue(':id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['role'];
    }


    /**
     * Modifies a user by their ID from the database.
     * @param user $user instance of user with the id to modify.
     */
    public static function ModifyUser($user)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery('UPDATE users SET name = :name, password = :password, role = :role, updated_at = :updated_at  WHERE id = :id');
        $query->bindValue(':id', $user->id, PDO::PARAM_INT);
        $query->bindValue(':name', $user->name, PDO::PARAM_STR);
        $query->bindValue(':password', password_hash($user->password, PASSWORD_DEFAULT));
        $query->bindValue(':updated_at', $user->updated_at);
        $query->bindValue(':role', $user->role, PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * Changes the user´s status by their ID from the database. 
     * @param int $id from user to be deleted. 
     */
    public static function DeleteUser($id)
    {
        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("UPDATE users SET status = :status, updated_at = :updated_at  WHERE id = :id");
        $date = new DateTime();
        $formated_date = $date->format('Y-m-d H:i:s');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':updated_at', $formated_date);
        $query->bindValue(':status', 0, PDO::PARAM_INT);
        $query->execute();
    }

    public static function LogIn($user_name,$user_pass){

        $objDataAccess = DataAccess::GetInstance();
        $query = $objDataAccess->PrepQuery("SELECT * FROM users WHERE name = :user_name AND status = :status");
        $query->bindValue(':user_name', $user_name);
        $query->bindValue(':status', 1, PDO::PARAM_INT);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($user_pass, $user['password'])) {
            return JwtAuth::CrearToken([$user['name'],$user['role']]);
        }
        throw new Exception('Credenciales Incorrectas');
    }
}