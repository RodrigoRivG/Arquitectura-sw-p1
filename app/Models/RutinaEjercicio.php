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

    /**
     * Deja a la rutina exactamente con los ejercicios recibidos.
     * $ejercicios: [ejercicio_id => ['series' => ?int, 'repeticiones' => ?int, 'orden' => ?int]]
     * Se invoca desde Rutina, dentro de la misma transacción que guarda la rutina.
     */
    public function reemplazarPorRutina(int $rutinaId, array $ejercicios): void
    {
        $borrar = $this->db->prepare('DELETE FROM rutina_ejercicio WHERE rutina_id = :rutina_id');
        $borrar->execute(['rutina_id' => $rutinaId]);

        $insertar = $this->db->prepare(
            'INSERT INTO rutina_ejercicio (rutina_id, ejercicio_id, series, repeticiones, orden)
             VALUES (:rutina_id, :ejercicio_id, :series, :repeticiones, :orden)'
        );

        foreach ($ejercicios as $ejercicioId => $datos) {
            $insertar->execute([
                'rutina_id' => $rutinaId,
                'ejercicio_id' => (int) $ejercicioId,
                'series' => $datos['series'],
                'repeticiones' => $datos['repeticiones'],
                'orden' => $datos['orden'],
            ]);
        }
    }
}
