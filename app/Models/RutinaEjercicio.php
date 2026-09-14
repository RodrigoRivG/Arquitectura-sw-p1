<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class RutinaEjercicio extends Model
{
    public function seleccionarPorRutina(int $rutinaId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ejercicio.id AS ejercicio_id, ejercicio.nombre AS ejercicio_nombre,
                    ejercicio.descripcion AS ejercicio_descripcion,
                    rutina_ejercicio.series, rutina_ejercicio.repeticiones, rutina_ejercicio.orden
             FROM rutina_ejercicio
             JOIN ejercicio ON ejercicio.id = rutina_ejercicio.ejercicio_id
             WHERE rutina_ejercicio.rutina_id = :rutina_id
             ORDER BY rutina_ejercicio.orden NULLS LAST, ejercicio.nombre'
        );
        $stmt->execute(['rutina_id' => $rutinaId]);

        return $stmt->fetchAll();
    }

    public function ejerciciosDisponibles(int $rutinaId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre FROM ejercicio
             WHERE id NOT IN (
                 SELECT ejercicio_id FROM rutina_ejercicio WHERE rutina_id = :rutina_id
             )
             ORDER BY nombre'
        );
        $stmt->execute(['rutina_id' => $rutinaId]);

        return $stmt->fetchAll();
    }

    public function asignar(int $rutinaId, int $ejercicioId, ?int $series, ?int $repeticiones, ?int $orden): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO rutina_ejercicio (rutina_id, ejercicio_id, series, repeticiones, orden)
             VALUES (:rutina_id, :ejercicio_id, :series, :repeticiones, :orden)'
        );
        $stmt->execute([
            'rutina_id' => $rutinaId,
            'ejercicio_id' => $ejercicioId,
            'series' => $series,
            'repeticiones' => $repeticiones,
            'orden' => $orden,
        ]);
    }

    public function quitar(int $rutinaId, int $ejercicioId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM rutina_ejercicio WHERE rutina_id = :rutina_id AND ejercicio_id = :ejercicio_id'
        );
        $stmt->execute(['rutina_id' => $rutinaId, 'ejercicio_id' => $ejercicioId]);
    }
}
