<?php

function GetUserRole($request){
    $header = $request->getHeaderLine('Authorization');
    if (strpos($header, 'Bearer') !== false) {
        $parts = explode("Bearer", $header);
        $token = trim($parts[1]);
        return JwtAuth::ObtenerData($token)[1];
    }
}

function GetUserID($request){
    $header = $request->getHeaderLine('Authorization');
    if (strpos($header, 'Bearer') !== false) {
        $parts = explode("Bearer", $header);
        $token = trim($parts[1]);
        $result = JwtAuth::ObtenerData($token)[0];
        return $result;
    }
}

function SaveImage($route, $name)
    {
        if (!isset($_FILES["image"]) || $_FILES["image"]["error"] != UPLOAD_ERR_OK) {
            throw new Exception("Error uploading the photo");
        }

        if ($_FILES["image"]['size'] > 100000) {
            throw new Exception("The image size is too big");
        }

        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $route = $route . $name . '.' . $extension;

        $directorio = dirname($route);
        if (!is_dir($directorio) || !is_writable($directorio)) {
            throw new Exception('The directory does not exists');
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $route)) {
            return true;
        } else {
            throw new Exception("Fail in moving the fiel.");
        }
    }
