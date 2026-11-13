<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Ejercicio;
use App\Models\Rutina;
use App\Models\RutinaEjercicio;
use App\Patterns\Builder\RutinaArmadaBuilder;
use App\Patterns\Builder\RutinaDirector;

class RutinaController extends Controller
{
    private Rutina $rutina;
    private Categoria $categoria;
    private Ejercicio $ejercicio;
    private RutinaEjercicio $rutinaEjercicio;

    public function __construct()
    {
        $this->requireAuth();
        $this->rutina = new Rutina();
        $this->categoria = new Categoria();
        $this->ejercicio = new Ejercicio();
        $this->rutinaEjercicio = new RutinaEjercicio();
    }

    public function listar(): void
    {
        $rutinas = $this->rutina->seleccionar();
        $this->render('rutinas/index', ['rutinas' => $rutinas, 'error' => null]);
    }

    public function crear(): void
    {
        $categorias = $this->categoria->seleccionar();
        $catalogo = $this->ejercicio->seleccionar();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $constructor = $this->armarRutina($categorias, $catalogo, $_POST, (array) ($_POST['ejercicios'] ?? []));
            $rutinaArmada = $constructor->obtenerResultado();

            if ($constructor->obtenerError() !== null) {
                $this->mostrarFormulario($rutinaArmada->datos(), $categorias, $catalogo, $rutinaArmada->ejercicios(), $constructor->obtenerError());
                return;
            }

            $this->rutina->insertar($rutinaArmada);
            $this->redirect('index.php?controller=rutina&action=listar');
            return;
        }

        $this->mostrarFormulario(null, $categorias, $catalogo, [], null);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $rutina = $this->rutina->buscarPorId($id);

        if ($rutina === null) {
            http_response_code(404);
            echo '<h1>404 - Rutina no encontrada</h1>';
            return;
        }

        $categorias = $this->categoria->seleccionar();
        $catalogo = $this->ejercicio->seleccionar();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $constructor = $this->armarRutina($categorias, $catalogo, $_POST, (array) ($_POST['ejercicios'] ?? []));
            $rutinaArmada = $constructor->obtenerResultado();

            if ($constructor->obtenerError() !== null) {
                $this->mostrarFormulario($rutinaArmada->datos() + ['id' => $id], $categorias, $catalogo, $rutinaArmada->ejercicios(), $constructor->obtenerError());
                return;
            }

            $this->rutina->actualizar($id, $rutinaArmada);
            $this->redirect('index.php?controller=rutina&action=listar');
            return;
        }

        // Precarga: la rutina guardada se arma con el mismo Director/Builder, a partir de la BD.
        $rutinaArmada = $this->armarRutina($categorias, $catalogo, $rutina, $this->rutinaEjercicio->seleccionarPorRutina($id))
            ->obtenerResultado();

        $this->mostrarFormulario($rutinaArmada->datos() + ['id' => $id], $categorias, $catalogo, $rutinaArmada->ejercicios(), null);
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $this->rutina->eliminar($id);
        $this->redirect('index.php?controller=rutina&action=listar');
    }

    /**
     * Cliente del patrón Builder: crea el constructor concreto, se lo entrega al Director,
     * le pide construir y devuelve el constructor para obtener el resultado (y el error, si hubo).
     */
    private function armarRutina(array $categorias, array $catalogo, array $rutina, array $ejercicios): RutinaArmadaBuilder
    {
        $constructor = new RutinaArmadaBuilder($categorias, $catalogo);
        $director = new RutinaDirector($constructor);
        $director->construir($rutina, $ejercicios);

        return $constructor;
    }

    private function mostrarFormulario(?array $rutina, array $categorias, array $catalogo, array $asignados, ?string $error): void
    {
        $this->render('rutinas/form', [
            'rutina' => $rutina,
            'categorias' => $categorias,
            'catalogo' => $catalogo,
            'asignados' => $asignados,
            'error' => $error,
        ]);
    }
}
