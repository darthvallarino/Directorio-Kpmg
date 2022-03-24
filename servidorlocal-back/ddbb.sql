CREATE DATABASE IF NOT EXISTS Directorio_Telefonico;
USE Directorio_Telefonico;

CREATE TABLE contactos(

    id          int auto_increment NOT null,
    nombre      varchar NOT null,
    telefono    bigint NOT null,
    email       varchar,
    direccion   text,
    imagen      varchar,

    CONSTRAINT pk_contactos PRIMARY KEY(id)

) ENGINE = InnoDb;

