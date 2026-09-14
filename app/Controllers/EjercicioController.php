<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ejercicio;
use App\Models\Video;
use PDOException;

class EjercicioController extends Controller
{
    private Ejercicio $ejercicio;
    private Video $video;

    public function __construct()
    {
        $this->requireAuth();
        $this->ejercicio = new Ejercicio();
        $this->video = new Video();
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

            $id = $this->ejercicio->insertar($datos['nombre'], $datos['descripcion']);

            if ($datos['video_titulo'] !== null) {
                $this->video->insertar($datos['video_titulo'], $datos['video_url'], $id);
            }

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
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $descripcion = trim((string) ($_POST['descripcion'] ?? '')) ?: null;

            if ($nombre === '') {
                $this->render('ejercicios/form', [
                    'ejercicio' => ['id' => $id, 'nombre' => $nombre, 'descripcion' => $descripcion],
                    'error' => 'El nombre es obligatorio.',
                ]);
                return;
            }

            $this->ejercicio->actualizar($id, $nombre, $descripcion);
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
        $videoTitulo = trim((string) ($_POST['video_titulo'] ?? ''));
        $videoUrl = trim((string) ($_POST['video_url'] ?? ''));

        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')) ?: null,
            'video_titulo' => $videoTitulo !== '' ? $videoTitulo : null,
            'video_url' => $videoUrl !== '' ? $videoUrl : null,
        ];
    }

    private function validar(array $datos): ?string
    {
        if ($datos['nombre'] === '') {
            return 'El nombre es obligatorio.';
        }

        if (($datos['video_titulo'] === null) !== ($datos['video_url'] === null)) {
            return 'Para agregar un video completá tanto el título como la URL.';
        }

        if ($datos['video_url'] !== null && !$this->urlValida($datos['video_url'])) {
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
