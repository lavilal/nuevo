<?php
namespace App\Controllers;

use App\Models\Alumno;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlumnoController
{
    public function index(Request $request, Response $response)
    {
        $alumnos = Alumno::with('carrera')->get();
        $response->getBody()->write(json_encode($alumnos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        
        // Manejo de la fotografía
        $uploadedFiles = $request->getUploadedFiles();
        $foto = $uploadedFiles['fotografia'];
        
        if ($foto->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($foto);
            $data['fotografia'] = $filename;
        }

        $alumno = Alumno::create($data);
        $response->getBody()->write(json_encode($alumno));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function moveUploadedFile($uploadedFile)
    {
        $directory = __DIR__ . '/../../public/uploads/fotos';
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}