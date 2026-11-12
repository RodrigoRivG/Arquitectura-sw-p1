<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Ejercicio;
use App\Models\Rutina;
use App\Models\RutinaEjercicio;

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
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos, $categorias, $catalogo);

            if ($error !== null) {
                $this->mostrarFormulario($datos, $categorias, $catalogo, $datos['ejercicios'], $error);
                return;
            }

            $this->rutina->insertar($datos['nombre'], $datos['descripcion'], $datos['nivel'], $datos['categoria_id'], $datos['ejercicios']);
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
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos, $categorias, $catalogo);

            if ($error !== null) {
                $datos['id'] = $id;
                $this->mostrarFormulario($datos, $categorias, $catalogo, $datos['ejercicios'], $error);
                return;
            }

            $this->rutina->actualizar($id, $datos['nombre'], $datos['descripcion'], $datos['nivel'], $datos['categoria_id'], $datos['ejercicios']);
            $this->redirect('index.php?controller=rutina&action=listar');
            return;
        }

        $this->mostrarFormulario($rutina, $categorias, $catalogo, $this->asignadosDeRutina($id), null);
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $this->rutina->eliminar($id);
        $this->redirect('index.php?controller=rutina&action=listar');
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

    /**
     * Ejercicios ya asignados a la rutina, indexados por ejercicio_id
     * (mismo formato que produce ejerciciosDelFormulario()).
     */
    private function asignadosDeRutina(int $rutinaId): array
    {
        $asignados = [];

        foreach ($this->rutinaEjercicio->seleccionarPorRutina($rutinaId) as $fila) {
            $asignados[(int) $fila['ejercicio_id']] = [
                'series' => $fila['series'],
                'repeticiones' => $fila['repeticiones'],
                'orden' => $fila['orden'],
            ];
        }

        return $asignados;
    }

    private function datosDelFormulario(): array
    {
        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')) ?: null,
            'nivel' => trim((string) ($_POST['nivel'] ?? '')) ?: null,
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
            'ejercicios' => $this->ejerciciosDelFormulario(),
        ];
    }

    /**
     * Solo se toman los ejercicios marcados en el formulario.
     * Resultado: [ejercicio_id => ['series' => ?int, 'repeticiones' => ?int, 'orden' => ?int]]
     */
    private function ejerciciosDelFormulario(): array
    {
        $ejercicios = [];

        foreach ((array) ($_POST['ejercicios'] ?? []) as $ejercicioId => $campos) {
            if (!is_array($campos) || !isset($campos['incluir'])) {
                continue;
            }

            $ejercicios[(int) $ejercicioId] = [
                'series' => $this->intOpcional((string) ($campos['series'] ?? '')),
                'repeticiones' => $this->intOpcional((string) ($campos['repeticiones'] ?? '')),
                'orden' => $this->intOpcional((string) ($campos['orden'] ?? '')),
            ];
        }

        return $ejercicios;
    }

    private function intOpcional(string $valor): ?int
    {
        $valor = trim($valor);

        return $valor === '' ? null : (int) $valor;
    }

    private function validar(array $datos, array $categorias, array $catalogo): ?string
    {
        if ($datos['nombre'] === '') {
            return 'El nombre es obligatorio.';
        }

        $idsCategorias = array_map('intval', array_column($categorias, 'id'));

        if (!in_array($datos['categoria_id'], $idsCategorias, true)) {
            return 'Debe seleccionar una categoría válida.';
        }

        $idsEjercicios = array_map('intval', array_column($catalogo, 'id'));

        foreach ($datos['ejercicios'] as $ejercicioId => $campos) {
            if (!in_array($ejercicioId, $idsEjercicios, true)) {
                return 'Se seleccionó un ejercicio que no existe en el catálogo.';
            }

            foreach (['series', 'repeticiones', 'orden'] as $campo) {
                if ($campos[$campo] !== null && $campos[$campo] < 0) {
                    return 'Series, repeticiones y orden no pueden ser negativos.';
                }
            }
        }

        return null;
    }
}
