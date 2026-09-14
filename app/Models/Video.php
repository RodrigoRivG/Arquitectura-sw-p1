<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Video extends Model
{
    public function seleccionarPorEjercicio(int $ejercicioId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, titulo, url, ejercicio_id FROM video WHERE ejercicio_id = :ejercicio_id ORDER BY id'
        );
        $stmt->execute(['ejercicio_id' => $ejercicioId]);

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, titulo, url, ejercicio_id FROM video WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $video = $stmt->fetch();

        return $video === false ? null : $video;
    }

    public function insertar(string $titulo, string $url, int $ejercicioId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO video (titulo, url, ejercicio_id) VALUES (:titulo, :url, :ejercicio_id)'
        );
        $stmt->execute(['titulo' => $titulo, 'url' => $url, 'ejercicio_id' => $ejercicioId]);
    }

    public function actualizar(int $id, string $titulo, string $url): void
    {
        $stmt = $this->db->prepare('UPDATE video SET titulo = :titulo, url = :url WHERE id = :id');
        $stmt->execute(['titulo' => $titulo, 'url' => $url, 'id' => $id]);
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM video WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
