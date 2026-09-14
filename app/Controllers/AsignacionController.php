<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ejercicio;
use App\Models\Rutina;
use App\Models\RutinaEjercicio;
use PDOException;

class AsignacionController extends Controller
{
    private RutinaEjercicio $asignacion;
    private Rutina $rutina;
    private Ejercicio $ejercicio;

    public function __construct()
    {
        $this->requireAuth();
        $this->asignacion = new RutinaEjercicio();
        $this->rutina = new Rutina();
        $this->ejercicio = new Ejercicio();
    }

    public function gestionar(): void
    {
        $rutinaId = (int) ($_GET['rutina_id'] ?? 0);
        $this->mostrarGestion($rutinaId, null);
    }

    public function asignar(): void
    {
        $rutinaId = (int) ($_POST['rutina_id'] ?? 0);
        $rutina = $this->rutina->buscarPorId($rutinaId);

        if ($rutina === null) {
            http_response_code(404);
            echo '<h1>404 - Rutina no encontrada</h1>';
            return;
        }

        $datos = $this->datosDelFormulario();
        $error = $this->validar($rutinaId, $datos);

        if ($error !== null) {
            $this->mostrarGestion($rutinaId, $error);
            return;
        }

        try {
            $this->asignacion->asignar($rutinaId, $datos['ejercicio_id'], $datos['series'], $datos['repeticiones'], $datos['orden']);
        } catch (PDOException $e) {
            $this->mostrarGestion($rutinaId, 'Ese ejercicio ya está asignado a esta rutina.');
            return;
        }

        $this->redirect('index.php?controller=asignacion&action=gestionar&rutina_id=' . $rutinaId);
    }

    public function quitar(): void
    {
        $rutinaId = (int) ($_POST['rutina_id'] ?? 0);
        $ejercicioId = (int) ($_POST['ejercicio_id'] ?? 0);

        $this->asignacion->quitar($rutinaId, $ejercicioId);
        $this->redirect('index.php?controller=asignacion&action=gestionar&rutina_id=' . $rutinaId);
    }

    private function mostrarGestion(int $rutinaId, ?string $error): void
    {
        $rutina = $this->rutina->buscarPorId($rutinaId);

        if ($rutina === null) {
            http_response_code(404);
            echo '<h1>404 - Rutina no encontrada</h1>';
            return;
        }

        $this->render('asignaciones/gestionar', [
            'rutina' => $rutina,
            'asignados' => $this->asignacion->seleccionarPorRutina($rutinaId),
            'disponibles' => $this->ejercicio->ejerciciosDisponibles($rutinaId),
            'error' => $error,
        ]);
    }

    private function datosDelFormulario(): array
    {
        return [
            'ejercicio_id' => (int) ($_POST['ejercicio_id'] ?? 0),
            'series' => $this->intOpcional($_POST['series'] ?? ''),
            'repeticiones' => $this->intOpcional($_POST['repeticiones'] ?? ''),
            'orden' => $this->intOpcional($_POST['orden'] ?? ''),
        ];
    }

    private function intOpcional(string $valor): ?int
    {
        $valor = trim($valor);

        return $valor === '' ? null : (int) $valor;
    }

    private function validar(int $rutinaId, array $datos): ?string
    {
        $disponibles = $this->ejercicio->ejerciciosDisponibles($rutinaId);
        $idsDisponibles = array_map('intval', array_column($disponibles, 'id'));

        if (!in_array($datos['ejercicio_id'], $idsDisponibles, true)) {
            return 'Debe seleccionar un ejercicio válido del catálogo.';
        }

        foreach (['series', 'repeticiones', 'orden'] as $campo) {
            if ($datos[$campo] !== null && $datos[$campo] < 0) {
                return 'Series, repeticiones y orden no pueden ser negativos.';
            }
        }

        return null;
    }
}
