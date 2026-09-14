<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ejercicio;
use App\Models\Video;

class VideoController extends Controller
{
    private Video $video;
    private Ejercicio $ejercicio;

    public function __construct()
    {
        $this->requireAuth();
        $this->video = new Video();
        $this->ejercicio = new Ejercicio();
    }

    public function listar(): void
    {
        $ejercicioId = (int) ($_GET['ejercicio_id'] ?? 0);
        $ejercicio = $this->ejercicio->buscarPorId($ejercicioId);

        if ($ejercicio === null) {
            http_response_code(404);
            echo '<h1>404 - Ejercicio no encontrado</h1>';
            return;
        }

        $videos = $this->video->seleccionarPorEjercicio($ejercicioId);
        $this->render('videos/index', ['ejercicio' => $ejercicio, 'videos' => $videos, 'error' => null]);
    }

    public function crear(): void
    {
        $ejercicioId = (int) ($_GET['ejercicio_id'] ?? 0);
        $ejercicio = $this->ejercicio->buscarPorId($ejercicioId);

        if ($ejercicio === null) {
            http_response_code(404);
            echo '<h1>404 - Ejercicio no encontrado</h1>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos);

            if ($error !== null) {
                $this->render('videos/form', ['ejercicio' => $ejercicio, 'video' => $datos, 'error' => $error]);
                return;
            }

            $this->video->insertar($datos['titulo'], $datos['url'], $ejercicioId);
            $this->redirect('index.php?controller=video&action=listar&ejercicio_id=' . $ejercicioId);
            return;
        }

        $this->render('videos/form', ['ejercicio' => $ejercicio, 'video' => null, 'error' => null]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $video = $this->video->buscarPorId($id);

        if ($video === null) {
            http_response_code(404);
            echo '<h1>404 - Video no encontrado</h1>';
            return;
        }

        $ejercicio = $this->ejercicio->buscarPorId((int) $video['ejercicio_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosDelFormulario();
            $error = $this->validar($datos);

            if ($error !== null) {
                $datos['id'] = $id;
                $this->render('videos/form', ['ejercicio' => $ejercicio, 'video' => $datos, 'error' => $error]);
                return;
            }

            $this->video->actualizar($id, $datos['titulo'], $datos['url']);
            $this->redirect('index.php?controller=video&action=listar&ejercicio_id=' . $video['ejercicio_id']);
            return;
        }

        $this->render('videos/form', ['ejercicio' => $ejercicio, 'video' => $video, 'error' => null]);
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $video = $this->video->buscarPorId($id);

        if ($video === null) {
            http_response_code(404);
            echo '<h1>404 - Video no encontrado</h1>';
            return;
        }

        $this->video->eliminar($id);
        $this->redirect('index.php?controller=video&action=listar&ejercicio_id=' . $video['ejercicio_id']);
    }

    private function datosDelFormulario(): array
    {
        return [
            'titulo' => trim((string) ($_POST['titulo'] ?? '')),
            'url' => trim((string) ($_POST['url'] ?? '')),
        ];
    }

    private function validar(array $datos): ?string
    {
        if ($datos['titulo'] === '') {
            return 'El título es obligatorio.';
        }

        if ($datos['url'] === '') {
            return 'La URL es obligatoria.';
        }

        if (!$this->urlValida($datos['url'])) {
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
