<?php
use Symfony\Component\Console\Helper\TableSeparator;
require_once './models/Table.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class TableController extends Table implements IApiUsable
{
    /**
     * Gets the body of the request and inserts a new Table in the db.
     * @return response 
     */
    public function AddOne($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $customer_count = $params['customer_count'];

            $table = new Table();
            $table->customer_count = $customer_count;
            $table->status = 1;
            $table->AddTable();

            $payload = json_encode(array("Message" => "Table created Sucessfully"));

        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to create new Table " . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /**
     * Gets the requests args and gets a table by their ID from the database.
     * @return response 
     */
    public function GetOne($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $table = $args['hex_code'];
        $table_coincidence = Table::GetOneTable($table);
        $payload = json_encode($table_coincidence);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets all the tables from the database.
     * @return response.
     */
    public function GetAll($request, $response, $args)
    {
        try {
            $list = Table::GetAllTables();
            $payload = json_encode(array("Tables:" => $list));
        } catch (Exception $ex) {
            $payload = json_encode(array("Message:" => 'Error trying to get all tables: ' . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets the query params from de request and modifies a table by their ID from the database.
     * @return response 
     */
    public function ModifyOne($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();

            $id = $query_params['id'];
            $customer_count = $query_params['customer_count'];
            $hex_code = $query_params['hex_code'];
            $status = $query_params['status'];

            $table = new Table();
            $table->id = $id;
            $table->hex_code = $hex_code;
            $table->customer_count = $customer_count;
            $table->status = $status;

            Table::ModifyTable($table);

            $payload = json_encode(array("Message" => "Table successfully modified"));
        } catch (Exception $ex) {
            $payload = json_encode(array("Message" => "Error atempting to modify table " . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets the body from de request and changes the table´s status by their ID from the database.
     * @return response 
     */
    public function DeleteOne($request, $response, $args)
    {
        try {
            $query_params = $request->getQueryParams();
            $id = $query_params['id'];

            Table::DeleteTable($id);

            $payload = json_encode(array("message" => "Table deleted succesfully"));
        } catch (Exception $ex) {

            $payload = json_encode(array("message" => "Error atempting to delete Table" . $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
