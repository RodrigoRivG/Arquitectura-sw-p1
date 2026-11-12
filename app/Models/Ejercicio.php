<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Ejercicio extends Model
{
    public function seleccionar(): array
    {
        $stmt = $this->db->query(
            'SELECT ejercicio.id, ejercicio.nombre, ejercicio.descripcion,
                    COUNT(video.id) AS total_videos
             FROM ejercicio
             LEFT JOIN video ON video.ejercicio_id = ejercicio.id
             GROUP BY ejercicio.id
             ORDER BY ejercicio.nombre'
        );

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nombre, descripcion FROM ejercicio WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $ejercicio = $stmt->fetch();

        return $ejercicio === false ? null : $ejercicio;
    }

    public function insertar(string $nombre, ?string $descripcion): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO ejercicio (nombre, descripcion) VALUES (:nombre, :descripcion) RETURNING id'
        );
        $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion]);

        return (int) $stmt->fetchColumn();
    }

    public function actualizar(int $id, string $nombre, ?string $descripcion): void
    {
        $stmt = $this->db->prepare(
            'UPDATE ejercicio SET nombre = :nombre, descripcion = :descripcion WHERE id = :id'
        );
        $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion, 'id' => $id]);
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM ejercicio WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
