CREATE DATABASE backstage_manager;

USE backstage_manager;

CREATE TABLE artisti (
    id_artista INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    nome_arte VARCHAR(100) NOT NULL,
    genere_musicale VARCHAR(50),
    email VARCHAR(100),
    telefono VARCHAR(20),
    citta VARCHAR(50),
    note TEXT
);

CREATE TABLE eventi (
    id_evento INT AUTO_INCREMENT PRIMARY KEY,
    id_artista INT NOT NULL,
    nome_evento VARCHAR(100) NOT NULL,
    data_evento DATE NOT NULL,
    luogo VARCHAR(100) NOT NULL,
    cachet DECIMAL(10,2),
    FOREIGN KEY (id_artista) REFERENCES artisti(id_artista)
);