<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Video demostrativo de un ejercicio. Relación 1 a 1: cada ejercicio tiene exactamente un video
 * (video.ejercicio_id es UNIQUE). Se gestiona desde Ejercicio, dentro de su misma transacción.
 */
class Video extends Model
{
    /**
     * Registra o actualiza el video del ejercicio.
     */
    public function guardarPorEjercicio(int $ejercicioId, string $titulo, string $url): void
    {
        $actualizar = $this->db->prepare(
            'UPDATE video SET titulo = :titulo, url = :url WHERE ejercicio_id = :ejercicio_id'
        );
        $actualizar->execute(['titulo' => $titulo, 'url' => $url, 'ejercicio_id' => $ejercicioId]);

        if ($actualizar->rowCount() > 0) {
            return;
        }

        $insertar = $this->db->prepare(
            'INSERT INTO video (titulo, url, ejercicio_id) VALUES (:titulo, :url, :ejercicio_id)'
        );
        $insertar->execute(['titulo' => $titulo, 'url' => $url, 'ejercicio_id' => $ejercicioId]);
    }
}
