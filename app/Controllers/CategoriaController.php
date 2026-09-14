<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;
use PDOException;

class CategoriaController extends Controller
{
    private Categoria $categoria;

    public function __construct()
    {
        $this->requireAuth();
        $this->categoria = new Categoria();
    }

    public function listar(): void
    {
        $categorias = $this->categoria->seleccionar();
        $this->render('categorias/index', ['categorias' => $categorias, 'error' => null]);
    }

    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $descripcion = trim((string) ($_POST['descripcion'] ?? '')) ?: null;

            if ($nombre === '') {
                $this->render('categorias/form', [
                    'categoria' => null,
                    'error' => 'El nombre es obligatorio.',
                ]);
                return;
            }

            $this->categoria->insertar($nombre, $descripcion);
            $this->redirect('index.php?controller=categoria&action=listar');
            return;
        }

        $this->render('categorias/form', ['categoria' => null, 'error' => null]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $categoria = $this->categoria->buscarPorId($id);

        if ($categoria === null) {
            http_response_code(404);
            echo '<h1>404 - Categoría no encontrada</h1>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $descripcion = trim((string) ($_POST['descripcion'] ?? '')) ?: null;

            if ($nombre === '') {
                $this->render('categorias/form', [
                    'categoria' => $categoria,
                    'error' => 'El nombre es obligatorio.',
                ]);
                return;
            }

            $this->categoria->actualizar($id, $nombre, $descripcion);
            $this->redirect('index.php?controller=categoria&action=listar');
            return;
        }

        $this->render('categorias/form', ['categoria' => $categoria, 'error' => null]);
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        try {
            $this->categoria->eliminar($id);
        } catch (PDOException $e) {
            $categorias = $this->categoria->seleccionar();
            $this->render('categorias/index', [
                'categorias' => $categorias,
                'error' => 'No se puede eliminar: la categoría tiene rutinas asociadas.',
            ]);
            return;
        }

        $this->redirect('index.php?controller=categoria&action=listar');
    }
}
