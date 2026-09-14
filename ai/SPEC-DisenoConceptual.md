# Diseño Conceptual del Modelo de Datos

> Este documento define el modelo conceptual de datos (entidades, atributos, relaciones y cardinalidades) que sustenta los casos de uso de `SPEC-CasosDeUso.md`.
> Es el paso previo al diagrama de clases / esquema físico de `Arquitectura.md`, que se actualizará una vez cerrado este diseño.
>
> Referencia visual: `ai/imagenes/DiseñoConceptualArqp1.drawio.png`. Las FK no se listan como atributos de las entidades — a nivel conceptual quedan representadas por las relaciones, no por columnas.

## Entidades

### Instructor

Entidad aislada: no participa en ninguna relación con el resto del modelo. Solo se usa para CU-01 (autenticación).

| Atributo | Dominio | Notas |
|---|---|---|
| id | Identificador único | PK |
| nombre | Texto | |
| email | Texto | Único |
| passwordHash | Texto | |

### Categoria

| Atributo | Dominio | Notas |
|---|---|---|
| id | Identificador único | PK |
| nombre | Texto | |
| descripcion | Texto | |

### Rutina

| Atributo | Dominio | Notas |
|---|---|---|
| id | Identificador único | PK |
| nombre | Texto | |
| descripcion | Texto | |
| nivel | Texto | |

### Ejercicio

Catálogo reutilizable, independiente de cualquier rutina puntual.

| Atributo | Dominio | Notas |
|---|---|---|
| id | Identificador único | PK |
| nombre | Texto | |
| descripcion | Texto | |

### Video

| Atributo | Dominio | Notas |
|---|---|---|
| id | Identificador único | PK |
| titulo | Texto | |
| url | Texto | |

## Relaciones y cardinalidades

| Relación | Entidades | Cardinalidad | Notas |
|---|---|---|---|
| Clasifica | Categoria (1) ↔ Rutina (0..\*) | 1:N | Toda rutina pertenece a exactamente una categoría; una categoría puede tener cero o más rutinas. No se puede eliminar una categoría con rutinas asociadas. |
| (sin nombre) | Rutina ↔ Ejercicio | N:M, resuelta por la clase asociativa `RutinaEjercicio` | Rutina y Ejercicio existen de forma independiente; se enlazan mediante la asociación, que agrega sus propios atributos. |
| Composición | Ejercicio ◆→ Video | 1:N (composición) | `Video` no existe sin su `Ejercicio`: es la parte de una composición UML (rombo relleno del lado de Ejercicio). Eliminar un ejercicio elimina en cascada sus videos. |

## Entidad asociativa: RutinaEjercicio

Resuelve la relación N:M entre `Rutina` y `Ejercicio` como clase asociativa (línea punteada hacia la asociación, no una entidad con FK propias a nivel conceptual).

| Atributo | Dominio | Notas |
|---|---|---|
| orden | Número | |
| series | Número | |
| repeticiones | Número | |

## Nota sobre Instructor

Se descarta la relación "administra" (Instructor → Rutina) que figuraba en el diagrama de clases de `Arquitectura.md`: al existir un único instructor, esa relación era una regla de negocio implícita y no una FK real entre tablas. `Instructor` queda fuera del modelo relacional de contenido.

## Próximos pasos

Este documento va a seguir cambiando a medida que se ajuste el diseño. Cuando se cierre, el diagrama de clases de `Arquitectura.md` debe actualizarse para reflejarlo (en particular, quitar la relación Instructor–Rutina).
