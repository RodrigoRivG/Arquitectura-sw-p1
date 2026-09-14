<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Rutina extends Model
{
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

    public function insertar(string $nombre, ?string $descripcion, ?string $nivel, int $categoriaId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO rutina (nombre, descripcion, nivel, categoria_id)
             VALUES (:nombre, :descripcion, :nivel, :categoria_id)'
        );
        $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'nivel' => $nivel,
            'categoria_id' => $categoriaId,
        ]);
    }

    public function actualizar(int $id, string $nombre, ?string $descripcion, ?string $nivel, int $categoriaId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE rutina SET nombre = :nombre, descripcion = :descripcion, nivel = :nivel, categoria_id = :categoria_id
             WHERE id = :id'
        );
        $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'nivel' => $nivel,
            'categoria_id' => $categoriaId,
            'id' => $id,
        ]);
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM rutina WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
