<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use Throwable;

class Ejercicio extends Model
{
    private Video $video;

    public function __construct()
    {
        parent::__construct();
        $this->video = new Video();
    }

    public function seleccionar(): array
    {
        $stmt = $this->db->query(
            'SELECT ejercicio.id, ejercicio.nombre, ejercicio.descripcion,
                    video.titulo AS video_titulo, video.url AS video_url
             FROM ejercicio
             LEFT JOIN video ON video.ejercicio_id = ejercicio.id
             ORDER BY ejercicio.nombre'
        );

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ejercicio.id, ejercicio.nombre, ejercicio.descripcion,
                    video.titulo AS video_titulo, video.url AS video_url
             FROM ejercicio
             LEFT JOIN video ON video.ejercicio_id = ejercicio.id
             WHERE ejercicio.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $ejercicio = $stmt->fetch();

        return $ejercicio === false ? null : $ejercicio;
    }

    /**
     * Registra el ejercicio y su video (obligatorio) en una sola transacción.
     */
    public function insertar(string $nombre, ?string $descripcion, string $videoTitulo, string $videoUrl): int
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO ejercicio (nombre, descripcion) VALUES (:nombre, :descripcion) RETURNING id'
            );
            $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion]);
            $id = (int) $stmt->fetchColumn();

            $this->video->guardarPorEjercicio($id, $videoTitulo, $videoUrl);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $id;
    }

    /**
     * Actualiza el ejercicio y su video en una sola transacción.
     */
    public function actualizar(int $id, string $nombre, ?string $descripcion, string $videoTitulo, string $videoUrl): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'UPDATE ejercicio SET nombre = :nombre, descripcion = :descripcion WHERE id = :id'
            );
            $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion, 'id' => $id]);

            $this->video->guardarPorEjercicio($id, $videoTitulo, $videoUrl);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM ejercicio WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
