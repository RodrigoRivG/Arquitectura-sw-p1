<?php

declare(strict_types=1);

namespace App\Patterns\Builder;

/**
 * Builder (GoF) — rol: Constructor (abstracto).
 * Declara los pasos para construir las partes de una rutina.
 */
abstract class RutinaBuilder
{
    /** Empieza una construcción nueva (descarta la anterior). */
    abstract public function reiniciar(): void;

    /** Parte 1: datos generales de la rutina. */
    abstract public function construirDatos(string $nombre, ?string $descripcion, ?string $nivel, int $categoriaId): void;

    /** Parte N: un ejercicio del catálogo asignado a la rutina. */
    abstract public function construirEjercicio(int $ejercicioId, ?int $series, ?int $repeticiones, ?int $orden): void;
}
