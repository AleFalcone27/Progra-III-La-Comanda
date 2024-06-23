<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UploadedFilesMiddleware
{
    private $media_type;
    private $new_path;

    private $uploaded_file;

    public function __construct($media_type, $new_path)
    {
        $this->media_type = $media_type;
        $this->new_path = $new_path;
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $uploadedFiles = $request->getUploadedFiles();

        if (
            !isset($uploadedFiles['file']) ||
            $uploadedFiles['file']->getError() !== UPLOAD_ERR_OK ||
            $uploadedFiles['file']->getClientMediaType() !== 'text/csv' ||
            (int) $uploadedFiles['file']->getSize() >= 100000
        ) {
            $response = new Response();
            if (!isset($uploadedFiles['file'])) {
                $response->getBody()->write(json_encode(["Error" => "No se encontró el archivo"]));
            } elseif ($uploadedFiles['file']->getError() !== UPLOAD_ERR_OK) {
                $response->getBody()->write(json_encode(["Error" => "Error al subir el archivo"]));
            } elseif ($uploadedFiles['file']->getClientMediaType() !== 'text/csv') {
                $response->getBody()->write(json_encode(["Error" => "El tipo de archivo no es el adecuado"]));
            } elseif ((int) $uploadedFiles['file']->getSize() >= 100000) {
                $response->getBody()->write(json_encode(["Error" => "El archivo es demasiado grande"]));
            }
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $uploadedFile = $uploadedFiles['file'];
        $uploadedFileName = $uploadedFile->getClientFilename();
        $destinationPath = $this->new_path . $uploadedFileName;

        if (file_exists($destinationPath)) {
            $response = new Response();
            $response->getBody()->write(json_encode(["Message" => "El archivo ya existe en el destino"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response = new Response();
        $response->getBody()->write(json_encode(["Message" => "El archivo ha sido guardado correctamente"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}