<?php
namespace App\Controllers;

use App\Models\Nota;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotaController
{
    public function index(Request $request, Response $response)
    {
        $notas = Nota::with(['alumno', 'curso', 'seccion', 'semestre'])->get();
        $response->getBody()->write(json_encode($notas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nota = Nota::create($data);
        
        $response->getBody()->write(json_encode($nota));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getNotasAlumno(Request $request, Response $response, $args)
    {
        $alumnoId = $args['id'];
        $notas = Nota::with(['curso', 'seccion', 'semestre'])
                     ->where('alumno_id', $alumnoId)
                     ->get();
                     
        $response->getBody()->write(json_encode($notas));
        return $response->withHeader('Content-Type', 'application/json');
    }
}