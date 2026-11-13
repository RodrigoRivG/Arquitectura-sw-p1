<?php

declare(strict_types=1);

namespace App\Patterns\TemplateMethod;

use App\Models\Ejercicio;

/**
 * Template Method (GoF) — rol: ClaseConcreta.
 * Variante "editar": carga el ejercicio existente (404 si no existe) y se guarda con actualizar().
 */
class EditarEjercicio extends GuardarEjercicio
{
    private int $id;

    public function __construct(Ejercicio $modelo, int $id)
    {
        parent::__construct($modelo);
        $this->id = $id;
    }

    protected function cargarEjercicio(): bool
    {
        $this->ejercicioActual = $this->modelo->buscarPorId($this->id);

        if ($this->ejercicioActual === null) {
            http_response_code(404);
            echo '<h1>404 - Ejercicio no encontrado</h1>';
            return false;
        }

        return true;
    }

    protected function persistir(array $datos): void
    {
        $this->modelo->actualizar($this->id, $datos['nombre'], $datos['descripcion'], $datos['video_titulo'], $datos['video_url']);
    }
}
