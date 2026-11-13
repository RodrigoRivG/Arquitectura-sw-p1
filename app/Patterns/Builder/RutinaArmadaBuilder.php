<?php

declare(strict_types=1);

namespace App\Patterns\Builder;

/**
 * Builder (GoF) — rol: ConstructorConcreto.
 * Construye una RutinaArmada validando cada parte a medida que se agrega.
 */
class RutinaArmadaBuilder extends RutinaBuilder
{
    private RutinaArmada $resultado;
    private ?string $error = null;

    /** @var int[] */
    private array $idsCategorias;

    /** @var int[] */
    private array $idsEjercicios;

    /**
     * @param array $categorias categorías existentes (para validar categoria_id)
     * @param array $catalogo   ejercicios del catálogo (para validar cada ejercicio)
     */
    public function __construct(array $categorias, array $catalogo)
    {
        $this->idsCategorias = array_map('intval', array_column($categorias, 'id'));
        $this->idsEjercicios = array_map('intval', array_column($catalogo, 'id'));
        $this->reiniciar();
    }

    public function reiniciar(): void
    {
        $this->resultado = new RutinaArmada();
        $this->error = null;
    }

    public function construirDatos(string $nombre, ?string $descripcion, ?string $nivel, int $categoriaId): void
    {
        if ($nombre === '') {
            $this->registrarError('El nombre es obligatorio.');
        }

        if (!in_array($categoriaId, $this->idsCategorias, true)) {
            $this->registrarError('Debe seleccionar una categoría válida.');
        }

        $this->resultado->establecerDatos($nombre, $descripcion, $nivel, $categoriaId);
    }

    public function construirEjercicio(int $ejercicioId, ?int $series, ?int $repeticiones, ?int $orden): void
    {
        if (!in_array($ejercicioId, $this->idsEjercicios, true)) {
            $this->registrarError('Se seleccionó un ejercicio que no existe en el catálogo.');
            return;
        }

        foreach ([$series, $repeticiones, $orden] as $valor) {
            if ($valor !== null && $valor < 0) {
                $this->registrarError('Series, repeticiones y orden no pueden ser negativos.');
                break;
            }
        }

        $this->resultado->agregarEjercicio($ejercicioId, $series, $repeticiones, $orden);
    }

    public function obtenerResultado(): RutinaArmada
    {
        return $this->resultado;
    }

    /** Primer error encontrado durante la construcción, o null si la rutina es válida. */
    public function obtenerError(): ?string
    {
        return $this->error;
    }

    private function registrarError(string $mensaje): void
    {
        if ($this->error === null) {
            $this->error = $mensaje;
        }
    }
}
