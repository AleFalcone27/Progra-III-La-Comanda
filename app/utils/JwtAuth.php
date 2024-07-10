<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuth
{
    private static $secret_key = 'AleFalcone';
    private static $enc_type = 'HS256';

    public static function CrearToken($data)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (30000),
            'aud' => self::Aud(),
            'data' => $data,
        );
        return JWT::encode($payload, self::$secret_key, self::$enc_type);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("The Token is empty");
        }
        try {
            $headers = new stdClass();
            $decodificado = JWT::decode (
                $token,
                new Key(self::$secret_key, 'HS256'),
                $headers
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("The user is not a valid one");
        }
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("The TOKEN Is missing.");
        }
        return JWT::decode(
            $token,
            self::$secret_key,
            self::$enc_type
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            new Key(self::$secret_key, 'HS256')
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}