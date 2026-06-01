CREATE TABLE Medewerkers

(
     Id                  INT             NOT NULL AUTO_INCREMENT
    ,GebruikerId         INT             NOT NULL
    ,Nummer              MEDIUMINT       NOT NULL
    ,Medewerkersoort     VARCHAR(20)     NOT NULL
    ,Isactief            BIT             NOT NULL
    ,Opmerking           VARCHAR(250)    NULL
    ,Datumaangemaakt     DATETIME(6)     NOT NULL
    ,Datumgewijzigd      DATETIME(6)     NOT NULL

    ,PRIMARY KEY (Id)

    ,UNIQUE KEY UK_Medewerker_Nummer (Nummer)

    ,CONSTRAINT FK_Medewerker_Gebruiker
        FOREIGN KEY (GebruikerId)
        REFERENCES Gebruiker(Id)
);

CREATE TABLE Voorstellingen
(
     Id                  INT             NOT NULL AUTO_INCREMENT
    ,MedewerkerId        INT             NOT NULL
    ,Naam                VARCHAR(100)    NOT NULL
    ,Beschrijving        TEXT            NULL
    ,Datum               DATE            NOT NULL
    ,Tijd                TIME            NOT NULL
    ,MaxAantalTickets    INT             NOT NULL
    ,Beschikbaarheid     VARCHAR(50)     NOT NULL
    ,Isactief            BIT             NOT NULL
    ,Opmerking           VARCHAR(250)    NULL
    ,Datumaangemaakt     DATETIME(6)     NOT NULL
    ,Datumgewijzigd      DATETIME(6)     NOT NULL

    ,PRIMARY KEY (Id)

    ,CONSTRAINT FK_Voorstelling_Medewerker
        FOREIGN KEY (MedewerkerId)
        REFERENCES Medewerker(Id)
);


create table tickets
(    
	 Id 				INT 			NOT NULL AUTO_INCREMENT
    ,BezoekerId 		INT 			NOT NULL
    ,VoorstellingId 	INT 			NOT NULL
    ,PrijsId 			INT 			NOT NULL
    ,Nummer 			MEDIUMINT 		NOT NULL
    ,Barcode 			VARCHAR(20) 	NOT NULL
    ,Datum 				DATE 			NOT NULL
    ,Tijd 				TIME 			NOT NULL
    ,TicketStatus 		VARCHAR(20) 	NOT NULL
    ,Isactief 			BIT 			NOT NULL
    ,Opmerking 			VARCHAR(250) 		NULL
    ,Datumaangemaakt 	DATETIME(6) 	NOT NULL
    ,Datumgewijzigd 	DATETIME(6) 	NOT NULL

    ,PRIMARY KEY (Id)

    ,UNIQUE KEY UK_Ticket_Nummer (Nummer)
    ,UNIQUE KEY UK_Ticket_Barcode (Barcode)

    ,CONSTRAINT FK_Ticket_Bezoeker
        FOREIGN KEY (BezoekerId)
        REFERENCES Bezoeker(Id)

    ,CONSTRAINT FK_Ticket_Voorstelling
        FOREIGN KEY (VoorstellingId)
        REFERENCES Voorstelling(Id)

    ,CONSTRAINT FK_Ticket_Prijs
        FOREIGN KEY (PrijsId)
        REFERENCES Prijs(Id)
);

