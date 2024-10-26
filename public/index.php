<?php
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

// Inicializar Eloquent
$capsule = new Capsule;
$capsule->addConnection(require __DIR__ . '/../config/database.php');
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Crear app
$app = AppFactory::create();

// Rutas de la API
$app->get('/api/alumnos', 'App\Controllers\AlumnoController:index');
$app->post('/api/alumnos', 'App\Controllers\AlumnoController:store');
$app->get('/api/carreras', 'App\Controllers\CarreraController:index');
$app->get('/api/cursos', 'App\Controllers\CursoController:index');
$app->get('/api/secciones', 'App\Controllers\SeccionController:index');
$app->post('/api/notas', 'App\Controllers\NotaController:store');
$app->get('/api/reportes/alumnos-por-carrera', 'App\Controllers\ReporteController:alumnosPorCarrera');
$app->get('/api/reportes/notas-por-carrera', 'App\Controllers\ReporteController:notasPorCarrera');
$app->get('/api/alumnos/{id}/notas', 'App\Controllers\NotaController:getNotasAlumno');
$app->get('/api/semestres', 'App\Controllers\SemestreController:index');

// Ruta para la página principal
$app->get('/', function ($request, $response) {
    $html = <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistema de Registro Académico</title>
        <style>
            /* ... (mantener los estilos existentes) ... */
        </style>
    </head>
    <body>
        <h1>Sistema de Registro Académico</h1>
        
        <div class="container">
            <div>
                <h2>Registro de Alumnos</h2>
                <form id="alumnoForm" enctype="multipart/form-data">
                    <input type="text" name="nombres" placeholder="Nombres" required>
                    <input type="text" name="apellidos" placeholder="Apellidos" required>
                    <input type="date" name="fecha_nacimiento" required>
                    <input type="file" name="fotografia" accept="image/*" required>
                    <select name="carrera_id" required>
                        <option value="">Seleccione una carrera</option>
                    </select>
                    <button type="submit">Registrar Alumno</button>
                </form>
            </div>

            <div>
                <h2>Lista de Alumnos</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Carrera</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAlumnos">
                    </tbody>
                </table>
            </div>
        </div>

        <div id="seccionNotas" style="display: none;">
            <h2>Notas del Alumno: <span id="nombreAlumno"></span></h2>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Sección</th>
                        <th>Semestre</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody id="tablaNotas">
                </tbody>
            </table>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar carreras al inicio
            fetch('/api/carreras')
                .then(response => response.json())
                .then(carreras => {
                    const select = document.querySelector('select[name="carrera_id"]');
                    carreras.forEach(carrera => {
                        const option = document.createElement('option');
                        option.value = carrera.id;
                        option.textContent = carrera.nombre;
                        select.appendChild(option);
                    });
                });

            // Cargar alumnos al inicio
            cargarAlumnos();

            // Manejar el envío del formulario
            document.getElementById('alumnoForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('/api/alumnos', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    alert('Alumno registrado exitosamente');
                    this.reset();
                    cargarAlumnos();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al registrar el alumno: ' + error.message);
                });
            });
        });

        function cargarAlumnos() {
            fetch('/api/alumnos')
                .then(response => response.json())
                .then(alumnos => {
                    const tabla = document.getElementById('tablaAlumnos');
                    tabla.innerHTML = '';
                    alumnos.forEach(alumno => {
                        const fila = `
                            <tr>
                                <td>\${alumno.nombres} \${alumno.apellidos}</td>
                                <td>\${alumno.fecha_nacimiento}</td>
                                <td>\${alumno.carrera ? alumno.carrera.nombre : 'No asignada'}</td>
                                <td>
                                    <img src="/uploads/alumnos/\${alumno.fotografia}" 
                                         alt="Foto de \${alumno.nombres}" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <button onclick="verNotas(\${alumno.id}, '\${alumno.nombres} \${alumno.apellidos}')">
                                        Ver Notas
                                    </button>
                                </td>
                            </tr>
                        `;
                        tabla.innerHTML += fila;
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function verNotas(alumnoId, nombreCompleto) {
            document.getElementById('nombreAlumno').textContent = nombreCompleto;
            
            fetch(`/api/alumnos/\${alumnoId}/notas`)
                .then(response => response.json())
                .then(notas => {
                    const tablaNotas = document.getElementById('tablaNotas');
                    tablaNotas.innerHTML = '';
                    
                    if (notas.length === 0) {
                        tablaNotas.innerHTML = '<tr><td colspan="4">No hay notas registradas</td></tr>';
                    } else {
                        notas.forEach(nota => {
                            const fila = `
                                <tr>
                                    <td>\${nota.curso ? nota.curso.nombre : 'No asignado'}</td>
                                    <td>\${nota.seccion ? nota.seccion.nombre : 'No asign
                                                                    <tr>
                                    <td>\${nota.curso ? nota.curso.nombre : 'No asignado'}</td>
                                    <td>\${nota.seccion ? nota.seccion.nombre : 'No asignada'}</td>
                                    <td>\${nota.semestre ? nota.semestre.nombre : 'No asignado'}</td>
                                    <td>\${nota.nota}</td>
                                </tr>
                            `;
                            tablaNotas.innerHTML += fila;
                        });
                    }

                    document.getElementById('seccionNotas').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar las notas');
                });
        }
        </script>
    </body>
    </html>
    HTML;

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

// Middleware para manejar CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Middleware para errores
$app->addErrorMiddleware(true, true, true);

// Ejecutar la aplicación
$app->run();