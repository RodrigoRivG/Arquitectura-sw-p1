<?php

declare(strict_types=1);

namespace App\Patterns\TemplateMethod;

use App\Core\Controller;
use App\Models\Ejercicio;

/**
 * Template Method (GoF) — rol: ClaseAbstracta.
 * Define el algoritmo para guardar un ejercicio (con su video) desde el formulario.
 * Crear y editar siguen exactamente los mismos pasos; solo cambian las dos
 * operaciones primitivas: cargarEjercicio() y persistir().
 */
abstract class GuardarEjercicio extends Controller
{
    protected Ejercicio $modelo;

    /** Ejercicio sobre el que se trabaja (null al crear). Lo establece cargarEjercicio(). */
    protected ?array $ejercicioActual = null;

    public function __construct(Ejercicio $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Método plantilla: fija el orden de los pasos. Es final: ninguna subclase puede alterarlo.
     */
    final public function ejecutar(): void
    {
        if (!$this->cargarEjercicio()) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->mostrarFormulario($this->ejercicioActual, null);
            return;
        }

        $datos = $this->leerFormulario();
        $error = $this->validar($datos);

        if ($error !== null) {
            $this->mostrarFormulario($datos, $error);
            return;
        }

        $this->persistir($datos);
        $this->redirigir();
    }

    /**
     * Operación primitiva 1: prepara el ejercicio sobre el que se trabaja.
     * Devuelve false si no se puede continuar (por ejemplo, el ejercicio no existe).
     */
    abstract protected function cargarEjercicio(): bool;

    /**
     * Operación primitiva 2: guarda los datos ya validados.
     */
    abstract protected function persistir(array $datos): void;

    // ----- Pasos comunes (iguales para crear y editar) -----

    protected function leerFormulario(): array
    {
        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')) ?: null,
            'video_titulo' => trim((string) ($_POST['video_titulo'] ?? '')),
            'video_url' => trim((string) ($_POST['video_url'] ?? '')),
        ];
    }

    protected function validar(array $datos): ?string
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

    protected function mostrarFormulario(?array $ejercicio, ?string $error): void
    {
        if ($ejercicio !== null && $this->ejercicioActual !== null) {
            $ejercicio['id'] = $this->ejercicioActual['id'];
        }

        $this->render('ejercicios/form', ['ejercicio' => $ejercicio, 'error' => $error]);
    }

    protected function redirigir(): void
    {
        $this->redirect('index.php?controller=ejercicio&action=listar');
    }

    private function urlValida(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }
}
