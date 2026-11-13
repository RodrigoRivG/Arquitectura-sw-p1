<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ejercicio;
use App\Patterns\TemplateMethod\CrearEjercicio;
use App\Patterns\TemplateMethod\EditarEjercicio;
use PDOException;

class EjercicioController extends Controller
{
    private Ejercicio $ejercicio;

    public function __construct()
    {
        $this->requireAuth();
        $this->ejercicio = new Ejercicio();
    }

    public function listar(): void
    {
        $ejercicios = $this->ejercicio->seleccionar();
        $this->render('ejercicios/index', ['ejercicios' => $ejercicios, 'error' => null]);
    }

    /** Cliente del Template Method: delega el algoritmo en la variante "crear". */
    public function crear(): void
    {
        (new CrearEjercicio($this->ejercicio))->ejecutar();
    }

    /** Cliente del Template Method: delega el algoritmo en la variante "editar". */
    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        (new EditarEjercicio($this->ejercicio, $id))->ejecutar();
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        try {
            $this->ejercicio->eliminar($id);
        } catch (PDOException $e) {
            $ejercicios = $this->ejercicio->seleccionar();
            $this->render('ejercicios/index', [
                'ejercicios' => $ejercicios,
                'error' => 'No se puede eliminar: el ejercicio está asignado a una o más rutinas.',
            ]);
            return;
        }

        $this->redirect('index.php?controller=ejercicio&action=listar');
    }
}
