<?php

declare(strict_types=1);

namespace App\Patterns\Builder;

/**
 * Builder (GoF) — rol: Director.
 * Conoce la estructura de una rutina (datos generales + lista de ejercicios) y
 * recorre esa estructura pidiéndole al constructor que construya cada parte.
 * Sirve igual para los datos del formulario que para los leídos de la base de datos,
 * porque ambos tienen la misma forma.
 */
class RutinaDirector
{
    private RutinaBuilder $constructor;

    public function __construct(RutinaBuilder $constructor)
    {
        $this->constructor = $constructor;
    }

    /**
     * @param array $rutina     claves: nombre, descripcion, nivel, categoria_id
     * @param array $ejercicios lista de [ejercicio_id, series, repeticiones, orden];
     *                          se omiten los que no traen ejercicio_id (no marcados en el formulario)
     */
    public function construir(array $rutina, array $ejercicios): void
    {
        $this->constructor->reiniciar();

        $this->constructor->construirDatos(
            $this->texto($rutina['nombre'] ?? null) ?? '',
            $this->texto($rutina['descripcion'] ?? null),
            $this->texto($rutina['nivel'] ?? null),
            (int) ($rutina['categoria_id'] ?? 0)
        );

        foreach ($ejercicios as $ejercicio) {
            if (!is_array($ejercicio) || empty($ejercicio['ejercicio_id'])) {
                continue;
            }

            $this->constructor->construirEjercicio(
                (int) $ejercicio['ejercicio_id'],
                $this->enteroOpcional($ejercicio['series'] ?? null),
                $this->enteroOpcional($ejercicio['repeticiones'] ?? null),
                $this->enteroOpcional($ejercicio['orden'] ?? null)
            );
        }
    }

    private function texto(mixed $valor): ?string
    {
        $valor = trim((string) ($valor ?? ''));

        return $valor === '' ? null : $valor;
    }

    private function enteroOpcional(mixed $valor): ?int
    {
        $valor = trim((string) ($valor ?? ''));

        return $valor === '' ? null : (int) $valor;
    }
}
