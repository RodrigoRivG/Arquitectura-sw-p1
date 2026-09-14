<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Rutina;

class RutinaController extends Controller
{
    private Rutina $rutina;
    private Categoria $categoria;

    public function __construct()
    {
        $this->requireAuth();
        $this->rutina = new Rutina();
        $this->categoria = new Categoria();
    }

    public function listar(): void
    {
        $rutinas = $this->rutina->seleccionar();
        $this->render('rutinas/index', ['rutinas' => $rutinas, 'error' => null]);
    }

    public function crear(): void
    {
        $categorias = $this->categoria->seleccionar();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos, $categorias);

            if ($error !== null) {
                $this->render('rutinas/form', [
                    'rutina' => $datos,
                    'categorias' => $categorias,
                    'error' => $error,
                ]);
                return;
            }

            $this->rutina->insertar($datos['nombre'], $datos['descripcion'], $datos['nivel'], $datos['categoria_id']);
            $this->redirect('index.php?controller=rutina&action=listar');
            return;
        }

        $this->render('rutinas/form', ['rutina' => null, 'categorias' => $categorias, 'error' => null]);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos, $categorias);

            if ($error !== null) {
                $datos['id'] = $id;
                $this->render('rutinas/form', [
                    'rutina' => $datos,
                    'categorias' => $categorias,
                    'error' => $error,
                ]);
                return;
            }

            $this->rutina->actualizar($id, $datos['nombre'], $datos['descripcion'], $datos['nivel'], $datos['categoria_id']);
            $this->redirect('index.php?controller=rutina&action=listar');
            return;
        }

        $this->render('rutinas/form', ['rutina' => $rutina, 'categorias' => $categorias, 'error' => null]);
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $this->rutina->eliminar($id);
        $this->redirect('index.php?controller=rutina&action=listar');
    }

    private function datosDelFormulario(): array
    {
        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')) ?: null,
            'nivel' => trim((string) ($_POST['nivel'] ?? '')) ?: null,
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
        ];
    }

    private function validar(array $datos, array $categorias): ?string
    {
        if ($datos['nombre'] === '') {
            return 'El nombre es obligatorio.';
        }

        $idsValidos = array_map('intval', array_column($categorias, 'id'));

        if (!in_array($datos['categoria_id'], $idsValidos, true)) {
            return 'Debe seleccionar una categoría válida.';
        }

        return null;
    }
}
