<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Categoria extends Model
{
    public function seleccionar(): array
    {
        $stmt = $this->db->query('SELECT id, nombre, descripcion FROM categoria ORDER BY nombre');

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nombre, descripcion FROM categoria WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $categoria = $stmt->fetch();

        return $categoria === false ? null : $categoria;
    }

    public function insertar(string $nombre, ?string $descripcion): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categoria (nombre, descripcion) VALUES (:nombre, :descripcion)'
        );
        $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion]);
    }

    public function actualizar(int $id, string $nombre, ?string $descripcion): void
    {
        $stmt = $this->db->prepare(
            'UPDATE categoria SET nombre = :nombre, descripcion = :descripcion WHERE id = :id'
        );
        $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion, 'id' => $id]);
    }

    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM categoria WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
