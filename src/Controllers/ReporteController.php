<?php
namespace App\Controllers;

use App\Models\Alumno;
use App\Models\Nota;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReporteController
{
    public function alumnosPorCarrera(Request $request, Response $response)
    {
        $alumnos = Alumno::with('carrera')
                        ->get()
                        ->groupBy('carrera.nombre');
                        
        $response->getBody()->write(json_encode($alumnos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function notasPorCarrera(Request $request, Response $response)
    {
        $notas = Nota::with(['alumno.carrera', 'curso', 'seccion'])
                     ->get()
                     ->groupBy('alumno.carrera.nombre');
                     
        $response->getBody()->write(json_encode($notas));
        return $response->withHeader('Content-Type', 'application/json');
    }
}