<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Patterns\Builder\RutinaArmada;
use Throwable;

class Rutina extends Model
{
    private RutinaEjercicio $rutinaEjercicio;

    public function __construct()
    {
        parent::__construct();
        $this->rutinaEjercicio = new RutinaEjercicio();
    }

    public function seleccionar(): array
    {
        $stmt = $this->db->query(
            'SELECT rutina.id, rutina.nombre, rutina.descripcion, rutina.nivel,
                    rutina.categoria_id, categoria.nombre AS categoria_nombre
             FROM rutina
             JOIN categoria ON categoria.id = rutina.categoria_id
             ORDER BY rutina.nombre'
        );

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, descripcion, nivel, categoria_id FROM rutina WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $rutina = $stmt->fetch();

        return $rutina === false ? null : $rutina;
    }

    public function buscarPorIdConCategoria(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT rutina.id, rutina.nombre, rutina.descripcion, rutina.nivel,
                    rutina.categoria_id, categoria.nombre AS categoria_nombre
             FROM rutina
             JOIN categoria ON categoria.id = rutina.categoria_id
             WHERE rutina.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $rutina = $stmt->fetch();

        return $rutina === false ? null : $rutina;
    }

    public function seleccionarPorCategoria(int $categoriaId): array
    {
        $stmt = $this->db->prepare(
            'SELECT rutina.id, rutina.nombre, rutina.descripcion, rutina.nivel,
                    rutina.categoria_id, categoria.nombre AS categoria_nombre
             FROM rutina
             JOIN categoria ON categoria.id = rutina.categoria_id
             WHERE rutina.categoria_id = :categoria_id
             ORDER BY rutina.nombre'
        );
        $stmt->execute(['categoria_id' => $categoriaId]);

        return $stmt->fetchAll();
    }

    /**
     * Registra la rutina (ya armada por el Builder) y sus ejercicios asignados en una sola transacción.
     */
    public function insertar(RutinaArmada $rutina): int
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO rutina (nombre, descripcion, nivel, categoria_id)
                 VALUES (:nombre, :descripcion, :nivel, :categoria_id)
                 RETURNING id'
            );
            $stmt->execute([
                'nombre' => $rutina->nombre(),
                'descripcion' => $rutina->descripcion(),
                'nivel' => $rutina->nivel(),
                'categoria_id' => $rutina->categoriaId(),
            ]);
            $id = (int) $stmt->fetchColumn();

            $this->rutinaEjercicio->reemplazarPorRutina($id, $rutina->ejercicios());

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $id;
    }

    /**
     * Actualiza la rutina y reemplaza sus ejercicios asignados en una sola transacción.
     */
    public function actualizar(int $id, RutinaArmada $rutina): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'UPDATE rutina SET nombre = :nombre, descripcion = :descripcion, nivel = :nivel, categoria_id = :categoria_id
                 WHERE id = :id'
            );
            $stmt->execute([
                'nombre' => $rutina->nombre(),
                'descripcion' => $rutina->descripcion(),
                'nivel' => $rutina->nivel(),
                'categoria_id' => $rutina->categoriaId(),
                'id' => $id,
            ]);

            $this->rutinaEjercicio->reemplazarPorRutina($id, $rutina->ejercicios());

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM rutina WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
