<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Rutina;
use App\Models\RutinaEjercicio;

class PublicController extends Controller
{
    private Categoria $categoria;
    private Rutina $rutina;
    private RutinaEjercicio $rutinaEjercicio;

    public function __construct()
    {
        $this->categoria = new Categoria();
        $this->rutina = new Rutina();
        $this->rutinaEjercicio = new RutinaEjercicio();
    }

    public function index(): void
    {
        $categoriaId = (int) ($_GET['categoria_id'] ?? 0);
        $categorias = $this->categoria->seleccionar();

        $rutinas = $categoriaId > 0
            ? $this->rutina->seleccionarPorCategoria($categoriaId)
            : $this->rutina->seleccionar();

        $this->render('public/index', [
            'categorias' => $categorias,
            'rutinas' => $rutinas,
            'categoriaSeleccionada' => $categoriaId,
        ]);
    }

    public function rutina(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $rutina = $this->rutina->buscarPorIdConCategoria($id);

        if ($rutina === null) {
            http_response_code(404);
            echo '<h1>404 - Rutina no encontrada</h1>';
            return;
        }

        $ejercicios = $this->rutinaEjercicio->seleccionarPorRutina($id);

        $this->render('public/rutina', [
            'rutina' => $rutina,
            'ejercicios' => $ejercicios,
        ]);
    }
}
