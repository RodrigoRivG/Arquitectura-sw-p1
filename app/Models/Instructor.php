<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Instructor extends Model
{
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, email, password_hash FROM instructor WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        $instructor = $stmt->fetch();

        return $instructor === false ? null : $instructor;
    }
}
