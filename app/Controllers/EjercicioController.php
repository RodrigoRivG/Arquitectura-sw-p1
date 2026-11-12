<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ejercicio;
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

    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos);

            if ($error !== null) {
                $this->render('ejercicios/form', ['ejercicio' => $datos, 'error' => $error]);
                return;
            }

            $this->ejercicio->insertar($datos['nombre'], $datos['descripcion'], $datos['video_titulo'], $datos['video_url']);
            $this->redirect('index.php?controller=ejercicio&action=listar');
            return;
        }

        $this->render('ejercicios/form', ['ejercicio' => null, 'error' => null]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $ejercicio = $this->ejercicio->buscarPorId($id);

        if ($ejercicio === null) {
            http_response_code(404);
            echo '<h1>404 - Ejercicio no encontrado</h1>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos);

            if ($error !== null) {
                $datos['id'] = $id;
                $this->render('ejercicios/form', ['ejercicio' => $datos, 'error' => $error]);
                return;
            }

            $this->ejercicio->actualizar($id, $datos['nombre'], $datos['descripcion'], $datos['video_titulo'], $datos['video_url']);
            $this->redirect('index.php?controller=ejercicio&action=listar');
            return;
        }

        $this->render('ejercicios/form', ['ejercicio' => $ejercicio, 'error' => null]);
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

    private function datosDelFormulario(): array
    {
        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')) ?: null,
            'video_titulo' => trim((string) ($_POST['video_titulo'] ?? '')),
            'video_url' => trim((string) ($_POST['video_url'] ?? '')),
        ];
    }

    private function validar(array $datos): ?string
    {
        if ($datos['nombre'] === '') {
            return 'El nombre es obligatorio.';
        }

        if ($datos['video_titulo'] === '' || $datos['video_url'] === '') {
            return 'El video es obligatorio: completá el título y la URL.';
        }

        if (!$this->urlValida($datos['video_url'])) {
            return 'La URL del video no es válida.';
        }

        return null;
    }

    private function urlValida(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }
}
