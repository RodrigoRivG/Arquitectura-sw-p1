<?php

declare(strict_types=1);

namespace App\Patterns\Builder;

/**
 * Builder (GoF) — rol: Producto.
 * Rutina completa, compuesta por sus datos generales y sus N ejercicios asignados.
 * Se construye paso a paso desde un RutinaBuilder.
 */
class RutinaArmada
{
    private string $nombre = '';
    private ?string $descripcion = null;
    private ?string $nivel = null;
    private int $categoriaId = 0;

    /** @var array<int, array{series: ?int, repeticiones: ?int, orden: ?int}> indexado por ejercicio_id */
    private array $ejercicios = [];

    public function establecerDatos(string $nombre, ?string $descripcion, ?string $nivel, int $categoriaId): void
    {
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->nivel = $nivel;
        $this->categoriaId = $categoriaId;
    }

    public function agregarEjercicio(int $ejercicioId, ?int $series, ?int $repeticiones, ?int $orden): void
    {
        $this->ejercicios[$ejercicioId] = [
            'series' => $series,
            'repeticiones' => $repeticiones,
            'orden' => $orden,
        ];
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function descripcion(): ?string
    {
        return $this->descripcion;
    }

    public function nivel(): ?string
    {
        return $this->nivel;
    }

    public function categoriaId(): int
    {
        return $this->categoriaId;
    }

    public function ejercicios(): array
    {
        return $this->ejercicios;
    }

    /**
     * Datos generales como array simple, para entregarlos a la vista.
     */
    public function datos(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'nivel' => $this->nivel,
            'categoria_id' => $this->categoriaId,
        ];
    }
}
