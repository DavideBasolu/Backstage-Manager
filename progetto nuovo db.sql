CREATE DATABASE GestionaleArtisti;
GO

USE GestionaleArtisti;
GO

CREATE TABLE UTENTI (
    idUtente INT IDENTITY(1,1) PRIMARY KEY,
    email NVARCHAR(100) NOT NULL UNIQUE,
    passwordHash NVARCHAR(255) NOT NULL,
    dataRegistrazione DATETIME NOT NULL DEFAULT GETDATE(),
    ruolo NVARCHAR(30) NOT NULL,
    statoAccount NVARCHAR(30) NOT NULL,
    CONSTRAINT CK_UTENTI_ruolo
        CHECK (ruolo IN ('artista', 'band', 'admin')),
    CONSTRAINT CK_UTENTI_statoAccount
        CHECK (statoAccount IN ('attivo', 'disattivato', 'sospeso'))
);
GO

CREATE TABLE ARTISTI (
    idArtista INT IDENTITY(1,1) PRIMARY KEY,
    nomeArte NVARCHAR(100) NOT NULL,
    genereMusicale NVARCHAR(50) NOT NULL,
    biografia NVARCHAR(MAX) NULL,
    fotoProfilo NVARCHAR(255) NULL,
    dataCreazione DATETIME NOT NULL DEFAULT GETDATE(),
    idUtente INT NOT NULL UNIQUE,
    CONSTRAINT FK_ARTISTI_UTENTI
        FOREIGN KEY (idUtente) REFERENCES UTENTI(idUtente)
);
GO

CREATE TABLE ETICHETTE (
    idEtichetta INT IDENTITY(1,1) PRIMARY KEY,
    nomeEtichetta NVARCHAR(100) NOT NULL,
    email NVARCHAR(100) NULL,
    telefono NVARCHAR(20) NULL,
    sitoWeb NVARCHAR(150) NULL,
    indirizzo NVARCHAR(150) NULL,
    note NVARCHAR(MAX) NULL,
    CONSTRAINT UQ_ETICHETTE_nomeEtichetta UNIQUE (nomeEtichetta)
);
GO

CREATE TABLE COLLABORAZIONI_ETICHETTA (
    idCollaborazioneEtichetta INT IDENTITY(1,1) PRIMARY KEY,
    dataInizio DATE NOT NULL,
    dataFine DATE NULL,
    statoCollaborazione NVARCHAR(30) NOT NULL,
    idArtista INT NOT NULL,
    idEtichetta INT NOT NULL,
    CONSTRAINT FK_COLLABORAZIONI_ETICHETTA_ARTISTI
        FOREIGN KEY (idArtista) REFERENCES ARTISTI(idArtista),
    CONSTRAINT FK_COLLABORAZIONI_ETICHETTA_ETICHETTE
        FOREIGN KEY (idEtichetta) REFERENCES ETICHETTE(idEtichetta),
    CONSTRAINT CK_COLLABORAZIONI_ETICHETTA_stato
        CHECK (statoCollaborazione IN ('attiva', 'conclusa', 'sospesa')),
    CONSTRAINT CK_COLLABORAZIONI_ETICHETTA_date
        CHECK (dataFine IS NULL OR dataFine >= dataInizio)
);
GO

CREATE TABLE LOCALI (
    idLocale INT IDENTITY(1,1) PRIMARY KEY,
    nomeLocale NVARCHAR(100) NOT NULL,
    indirizzo NVARCHAR(150) NOT NULL,
    citta NVARCHAR(100) NOT NULL,
    cap NVARCHAR(10) NULL,
    capienza INT NULL,
    note NVARCHAR(MAX) NULL,
    CONSTRAINT CK_LOCALI_capienza
        CHECK (capienza IS NULL OR capienza > 0)
);
GO

CREATE TABLE CONTATTI_LOCALI (
    idContattoLocale INT IDENTITY(1,1) PRIMARY KEY,
    nomeReferente NVARCHAR(50) NOT NULL,
    cognomeReferente NVARCHAR(50) NOT NULL,
    telefono NVARCHAR(20) NOT NULL,
    email NVARCHAR(100) NULL,
    ruolo NVARCHAR(50) NULL,
    note NVARCHAR(MAX) NULL,
    idLocale INT NOT NULL,
    CONSTRAINT FK_CONTATTI_LOCALI_LOCALI
        FOREIGN KEY (idLocale) REFERENCES LOCALI(idLocale)
);
GO

CREATE TABLE STATI_CONCERTO (
    idStatoConcerto INT IDENTITY(1,1) PRIMARY KEY,
    nomeStato NVARCHAR(30) NOT NULL UNIQUE
);
GO

CREATE TABLE CONCERTI (
    idConcerto INT IDENTITY(1,1) PRIMARY KEY,
    dataEvento DATE NOT NULL,
    oraEvento TIME NOT NULL,
    cachet DECIMAL(10,2) NULL,
    noteLogistiche NVARCHAR(MAX) NULL,
    noteInterne NVARCHAR(MAX) NULL,
    idArtista INT NOT NULL,
    idLocale INT NOT NULL,
    idStatoConcerto INT NOT NULL,
    CONSTRAINT FK_CONCERTI_ARTISTI
        FOREIGN KEY (idArtista) REFERENCES ARTISTI(idArtista),
    CONSTRAINT FK_CONCERTI_LOCALI
        FOREIGN KEY (idLocale) REFERENCES LOCALI(idLocale),
    CONSTRAINT FK_CONCERTI_STATI_CONCERTO
        FOREIGN KEY (idStatoConcerto) REFERENCES STATI_CONCERTO(idStatoConcerto),
    CONSTRAINT CK_CONCERTI_cachet
        CHECK (cachet IS NULL OR cachet >= 0)
);
GO

CREATE TABLE BRANI (
    idBrano INT IDENTITY(1,1) PRIMARY KEY,
    titolo NVARCHAR(150) NOT NULL,
    durata TIME NOT NULL,
    bpm SMALLINT NULL,
    tonalita NVARCHAR(20) NULL,
    annoPubblicazione INT NULL,
    genereBrano NVARCHAR(50) NULL,
    testo NVARCHAR(MAX) NULL,
    statoBrano NVARCHAR(30) NOT NULL,
    note NVARCHAR(MAX) NULL,
    idArtista INT NOT NULL,
    CONSTRAINT FK_BRANI_ARTISTI
        FOREIGN KEY (idArtista) REFERENCES ARTISTI(idArtista),
    CONSTRAINT CK_BRANI_bpm
        CHECK (bpm IS NULL OR bpm > 0),
    CONSTRAINT CK_BRANI_annoPubblicazione
        CHECK (annoPubblicazione IS NULL OR annoPubblicazione >= 1900),
    CONSTRAINT CK_BRANI_statoBrano
        CHECK (statoBrano IN ('pubblicato', 'inedito', 'demo', 'cover'))
);
GO

CREATE TABLE ARTISTI_OSPITI (
    idOspite INT IDENTITY(1,1) PRIMARY KEY,
    nomeArte NVARCHAR(100) NOT NULL,
    genereMusicale NVARCHAR(50) NULL,
    contatto NVARCHAR(100) NULL,
    note NVARCHAR(MAX) NULL
);
GO

CREATE TABLE FEAT_BRANI (
    idFeatBrano INT IDENTITY(1,1) PRIMARY KEY,
    tipoCollaborazione NVARCHAR(50) NULL,
    idBrano INT NOT NULL,
    idOspite INT NOT NULL,
    CONSTRAINT FK_FEAT_BRANI_BRANI
        FOREIGN KEY (idBrano) REFERENCES BRANI(idBrano),
    CONSTRAINT FK_FEAT_BRANI_ARTISTI_OSPITI
        FOREIGN KEY (idOspite) REFERENCES ARTISTI_OSPITI(idOspite),
    CONSTRAINT UQ_FEAT_BRANI UNIQUE (idBrano, idOspite)
);
GO

CREATE TABLE SETLIST (
    idSetlist INT IDENTITY(1,1) PRIMARY KEY,
    nomeSetlist NVARCHAR(100) NOT NULL,
    descrizione NVARCHAR(MAX) NULL,
    durataStimata TIME NULL,
    note NVARCHAR(MAX) NULL,
    dataCreazione DATETIME NOT NULL DEFAULT GETDATE(),
    idArtista INT NOT NULL,
    CONSTRAINT FK_SETLIST_ARTISTI
        FOREIGN KEY (idArtista) REFERENCES ARTISTI(idArtista)
);
GO

CREATE TABLE SETLIST_BRANI (
    idSetlistBrano INT IDENTITY(1,1) PRIMARY KEY,
    posizione INT NOT NULL,
    noteEsecuzione NVARCHAR(MAX) NULL,
    idSetlist INT NOT NULL,
    idBrano INT NOT NULL,
    CONSTRAINT FK_SETLIST_BRANI_SETLIST
        FOREIGN KEY (idSetlist) REFERENCES SETLIST(idSetlist),
    CONSTRAINT FK_SETLIST_BRANI_BRANI
        FOREIGN KEY (idBrano) REFERENCES BRANI(idBrano),
    CONSTRAINT CK_SETLIST_BRANI_posizione
        CHECK (posizione > 0),
    CONSTRAINT UQ_SETLIST_BRANI_posizione UNIQUE (idSetlist, posizione),
    CONSTRAINT UQ_SETLIST_BRANI_brano UNIQUE (idSetlist, idBrano)
);
GO

CREATE TABLE CONCERTI_SETLIST (
    idConcertoSetlist INT IDENTITY(1,1) PRIMARY KEY,
    idConcerto INT NOT NULL,
    idSetlist INT NOT NULL,
    CONSTRAINT FK_CONCERTI_SETLIST_CONCERTI
        FOREIGN KEY (idConcerto) REFERENCES CONCERTI(idConcerto),
    CONSTRAINT FK_CONCERTI_SETLIST_SETLIST
        FOREIGN KEY (idSetlist) REFERENCES SETLIST(idSetlist),
    CONSTRAINT UQ_CONCERTI_SETLIST UNIQUE (idConcerto, idSetlist)
);
GO