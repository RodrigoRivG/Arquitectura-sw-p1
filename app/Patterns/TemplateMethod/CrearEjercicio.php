<?php

declare(strict_types=1);

namespace App\Patterns\TemplateMethod;

/**
 * Template Method (GoF) — rol: ClaseConcreta.
 * Variante "crear": no hay ejercicio previo y se guarda con insertar().
 */
class CrearEjercicio extends GuardarEjercicio
{
    protected function cargarEjercicio(): bool
    {
        $this->ejercicioActual = null;

        return true;
    }

    protected function persistir(array $datos): void
    {
        $this->modelo->insertar($datos['nombre'], $datos['descripcion'], $datos['video_titulo'], $datos['video_url']);
    }
}
