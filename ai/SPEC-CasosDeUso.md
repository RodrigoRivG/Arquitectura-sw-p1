# Especificación de Casos de Uso

> Este documento define **qué** hace el sistema (funcionalidad, actores, casos de uso).
> El **cómo** (patrón arquitectónico, estructura de carpetas, diagramas técnicos) se define en `Arquitectura.md`.

## Contexto

Plataforma web para un instructor de gimnasio que publica rutinas de entrenamiento (con ejercicios y videos) organizadas por categoría, para que sus alumnos/clientes las consulten libremente. El instructor administra el contenido mediante una sección con login.

## Actores

| Actor | Descripción | Requiere autenticación |
|---|---|---|
| **Instructor** | Único usuario administrador. Crea y mantiene categorías, rutinas, ejercicios y videos. | Sí |
| **Cliente/Alumno** | Visitante público del sitio. Solo consulta contenido. | No |

## Entidades involucradas

`Instructor`, `Categoria`, `Rutina`, `Ejercicio`, `Video` — el detalle de atributos y relaciones vive en el diagrama de clases de `Arquitectura.md`.

## Casos de uso

Cada CU de gestión consolida el CRUD completo (crear, listar, editar, eliminar) de su entidad en un único caso de uso, con esas operaciones como flujos del mismo CU.

### CU-01 | Iniciar sesión (Acceso — Instructor)

| Campo | Detalle |
|---|---|
| Descripción | El instructor se autentica para acceder al panel de administración. Incluye, como flujo secundario, el cierre de sesión. |
| Precondición | El instructor tiene credenciales válidas. |
| Flujo principal | 1. Ingresa usuario/contraseña. 2. El sistema valida contra la BD. 3. Se crea la sesión. |
| Flujo secundario | Cerrar sesión: el instructor solicita cerrar sesión; el sistema destruye la sesión y vuelve a tratarlo como visitante público. |
| Postcondición | Sesión activa; acceso a las funciones de administración (o sesión finalizada, en el flujo secundario). |

### CU-02 | Gestionar categorías (Instructor)

| Campo | Detalle |
|---|---|
| Descripción | CRUD completo de categorías (ej. Fuerza, Cardio). |
| Precondición | Sesión activa. |
| Flujos | Crear · Listar · Editar · Eliminar (requiere que la categoría no tenga rutinas asociadas, o se define regla de cascada). |
| Postcondición | Categoría creada / listada / actualizada / eliminada según el flujo ejecutado. |

### CU-03 | Gestionar rutinas (Instructor)

| Campo | Detalle |
|---|---|
| Descripción | CRUD completo de rutinas, cada una asociada a una categoría. |
| Precondición | Sesión activa; para crear, existe al menos una categoría. |
| Flujos | Crear · Listar (vista de administración) · Editar · Eliminar (elimina también los ejercicios/videos asociados). |
| Postcondición | Rutina creada / listada / actualizada / eliminada según el flujo ejecutado. |

### CU-04 | Gestionar ejercicios (Instructor)

`Ejercicio` es un catálogo reutilizable, independiente de cualquier rutina puntual (ver CU-05).

| Campo | Detalle |
|---|---|
| Descripción | CRUD completo del catálogo de ejercicios. |
| Precondición | Sesión activa. |
| Flujos | Crear · Listar · Editar · Eliminar (elimina en cascada sus videos y sus asignaciones a rutinas). |
| Postcondición | Ejercicio creado / listado / actualizado / eliminado según el flujo ejecutado. |

### CU-05 | Asignar ejercicio a rutina (Instructor)

Relación N a M: un ejercicio del catálogo puede usarse en varias rutinas, y una rutina puede incluir varios ejercicios; cada asignación define sus propios `series`/`repeticiones`/`orden`.

| Campo | Detalle |
|---|---|
| Descripción | Asocia y desasocia ejercicios del catálogo a una rutina, definiendo series/repeticiones/orden para esa rutina. |
| Precondición | Sesión activa; rutina y ejercicio existen (para desasociar, la asociación existe). |
| Flujos | Asignar ejercicio a rutina · Quitar ejercicio de una rutina (el ejercicio permanece en el catálogo). |
| Postcondición | Asociación `RutinaEjercicio` creada o eliminada según el flujo ejecutado. |

### CU-06 | Gestionar videos (Instructor)

| Campo | Detalle |
|---|---|
| Descripción | CRUD completo de videos demostrativos asociados a un ejercicio. |
| Precondición | Sesión activa; ejercicio existe. |
| Flujos | Subir video · Listar videos de un ejercicio · Editar (título/url) · Eliminar. |
| Postcondición | Video creado / listado / actualizado / eliminado según el flujo ejecutado. |

### Consulta pública (Cliente/Alumno) — fuera de alcance por ahora

> Se deja documentada para una futura iteración; no forma parte del conteo actual de casos de uso.

| ID | Nombre | Descripción | Precondición | Postcondición |
|---|---|---|---|---|
| UC-P1 | Ver rutinas por categoría | Navega el catálogo público de rutinas, filtrable por categoría. | Ninguna (acceso libre). | — |
| UC-P2 | Ver detalle de rutina | Consulta una rutina con su lista de ejercicios asignados (vía `RutinaEjercicio`). | La rutina existe. | — |
| UC-P3 | Reproducir video de ejercicio | Reproduce el/los video(s) asociados a un ejercicio. | El ejercicio tiene al menos un video. | — |

## Clasificación de complejidad de los casos de uso

Cada caso de uso se clasifica según el tipo de relación entre tablas que involucra al ejecutarse:

| Complejidad | Relación | Criterio |
|---|---|---|
| **Simple** | 1 a 1 | Opera sobre una sola tabla, sin recorrer ninguna relación (CRUD directo de una entidad). |
| **Medio** | 1 a N | Involucra una relación de uno a muchos (ej. listar los hijos de un padre, o crear/consultar un registro atravesando esa relación). |
| **Complejo** | N a M | Involucra una relación muchos a muchos, resuelta mediante una tabla intermedia. |

| ID | Caso de uso | Complejidad | Relación involucrada |
|---|---|---|---|
| CU-01 | Iniciar sesión | Simple | Solo `instructor` |
| CU-02 | Gestionar categorías | Simple | Solo `categoria` |
| CU-03 | Gestionar rutinas | Medio | `categoria` 1→N `rutina` |
| CU-04 | Gestionar ejercicios | Simple | Solo `ejercicio` (catálogo propio) |
| CU-05 | Asignar ejercicio a rutina | Complejo | `rutina` N↔M `ejercicio` vía `rutina_ejercicio` |
| CU-06 | Gestionar videos | Medio | `ejercicio` 1→N `video` |

## Resumen de alcance

- **6 casos de uso** en total: acceso (login/logout), y CRUD consolidado de Categoria/Rutina/Ejercicio/Video, más la asignación N a M entre Rutina y Ejercicio.
- Cubre los tres niveles de complejidad requeridos: **simple** (1 a 1), **medio** (1 a N) y **complejo** (N a M vía `rutina_ejercicio`).
- La **consulta pública** (3 casos de uso adicionales) queda documentada pero fuera del alcance/conteo actual.
- El detalle visual de las vistas (HTML/CSS) no es objeto de evaluación; sí lo es que cada caso de uso quede correctamente soportado por el flujo Modelo-Vista-Controlador definido en `Arquitectura.md`.
