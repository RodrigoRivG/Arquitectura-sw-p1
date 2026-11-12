-- Esquema de base de datos (PostgreSQL)
-- Corresponde al diseño conceptual de ai/SPEC-DisenoConceptual.md

CREATE TABLE instructor (
    id            SERIAL PRIMARY KEY,
    nombre        VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    creado_en     TIMESTAMP NOT NULL DEFAULT now()
);

CREATE TABLE categoria (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE rutina (
    id            SERIAL PRIMARY KEY,
    nombre        VARCHAR(150) NOT NULL,
    descripcion   TEXT,
    nivel         VARCHAR(50),
    categoria_id  INTEGER NOT NULL REFERENCES categoria(id) ON DELETE RESTRICT,
    creado_en     TIMESTAMP NOT NULL DEFAULT now()
);

-- Catálogo de ejercicios, reutilizable entre rutinas (relación N a M con rutina)
CREATE TABLE ejercicio (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    descripcion TEXT
);

-- Tabla intermedia: asigna un ejercicio del catálogo a una rutina,
-- con series/repeticiones/orden propios de esa asignación (relación N a M)
CREATE TABLE rutina_ejercicio (
    rutina_id    INTEGER NOT NULL REFERENCES rutina(id)    ON DELETE CASCADE,
    ejercicio_id INTEGER NOT NULL REFERENCES ejercicio(id) ON DELETE CASCADE,
    series       INTEGER,
    repeticiones INTEGER,
    orden        INTEGER,
    PRIMARY KEY (rutina_id, ejercicio_id)
);

-- Video demostrativo: relación 1 a 1 con ejercicio (cada ejercicio tiene exactamente un video)
CREATE TABLE video (
    id           SERIAL PRIMARY KEY,
    titulo       VARCHAR(150) NOT NULL,
    url          VARCHAR(500) NOT NULL,
    ejercicio_id INTEGER NOT NULL UNIQUE REFERENCES ejercicio(id) ON DELETE CASCADE
);

-- Índices sobre llaves foráneas (aceleran los JOIN de las consultas de listado)
CREATE INDEX idx_rutina_categoria_id     ON rutina(categoria_id);
CREATE INDEX idx_rutina_ejercicio_rutina ON rutina_ejercicio(rutina_id);
CREATE INDEX idx_rutina_ejercicio_ejerc  ON rutina_ejercicio(ejercicio_id);
