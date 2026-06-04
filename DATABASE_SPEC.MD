# Database Schema Specification (v1.0)

## Relationship Overview (Logic from ERD)
- **Gebruiker** is the central entity.
- One **Gebruiker** has: One **Rol**, One **Contact**, and can be either a **Bezoeker** or a **Medewerker** (1-to-1 relationships via GebruikerId).
- **Voorstelling** is managed by a **Medewerker** (FK: MedewerkerId).
- **Ticket** connects three things: **Bezoeker**, **Voorstelling**, and **Prijs**.
- **Melding** (Notification) can be sent by/to a **Bezoeker** or a **Medewerker**.

## Standard Columns (Required on ALL Tables)
Every table below MUST include these columns:
- `Isactief` (BIT, NOT NULL)
- `Opmerking` (VARCHAR 250, NULLABLE)
- `Datumaangemaakt` (DATETIME(6), NOT NULL)
- `Datumgewijzigd` (DATETIME(6), NOT NULL)

---

## Table Definitions

### 1. Gebruiker (Users)
- `Id` (INT, PK, NOT NULL)
- `Voornaam` (VARCHAR 50, NOT NULL)
- `Tussenvoegsel` (VARCHAR 10, NULLABLE)
- `Achternaam` (VARCHAR 50, NOT NULL)
- `Gebruikersnaam` (VARCHAR 100, NOT NULL)
- `Wachtwoord` (VARCHAR 255, NOT NULL) - Encrypted
- `IsIngelogd` (BIT, NOT NULL)
- `Ingelogd` (DATETIME, NULLABLE)
- `Uitgelogd` (DATETIME, NULLABLE)

### 2. Rol (Roles)
- `Id` (INT, PK, NOT NULL)
- `GebruikerId` (INT, FK -> Gebruiker.Id, NOT NULL)
- `Naam` (VARCHAR 100, NOT NULL) - (Bezoeker, Medewerker, Administrator)

### 3. Contact (Contact Info)
- `Id` (INT, PK, NOT NULL)
- `GebruikerId` (INT, FK -> Gebruiker.Id, NOT NULL)
- `Email` (VARCHAR 100, NOT NULL)
- `Mobiel` (VARCHAR 20, NOT NULL)

### 4. Medewerker (Employees)
- `Id` (INT, PK, NOT NULL)
- `GebruikerId` (INT, FK -> Gebruiker.Id, NOT NULL)
- `Nummer` (MEDIUMINT, NOT NULL) - Unique ID
- `Medewerkersoort` (VARCHAR 20, NOT NULL) - (e.g., Beheerder, Ticketcontroleur)

### 5. Bezoeker (Visitors)
- `Id` (INT, PK, NOT NULL)
- `GebruikerId` (INT, FK -> Gebruiker.Id, NOT NULL)
- `Relatienummer` (MEDIUMINT, NOT NULL) - Unique ID

### 6. Prijs (Pricing)
- `Id` (INT, PK, NOT NULL)
- `Tarief` (DECIMAL 5,2, NOT NULL)

### 7. Voorstelling (Performances)
- `Id` (INT, PK, NOT NULL)
- `MedewerkerId` (INT, FK -> Medewerker.Id, NOT NULL)
- `Naam` (VARCHAR 100, NOT NULL)
- `Beschrijving` (TEXT, NULLABLE)
- `Datum` (DATE, NOT NULL)
- `Tijd` (TIME, NOT NULL)
- `MaxAantalTickets` (INT, NOT NULL)
- `Beschikbaarheid` (VARCHAR 50, NOT NULL) - (Ingepland, Uitverkocht, Geannuleerd)

### 8. Ticket
- `Id` (INT, PK, NOT NULL)
- `BezoekerId` (INT, FK -> Bezoeker.Id, NOT NULL)
- `VoorstellingId` (INT, FK -> Voorstelling.Id, NOT NULL)
- `PrijsId` (INT, FK -> Prijs.Id, NOT NULL)
- `Nummer` (MEDIUMINT, NOT NULL) - Unique reservation number
- `Barcode` (VARCHAR 20, NOT NULL)
- `Datum` (DATE, NOT NULL)
- `Tijd` (TIME, NOT NULL)
- `Status` (VARCHAR 20, NOT NULL) - (Vrij, Bezet, Gereserveerd, Geannuleerd)

### 9. Melding (Notifications/Feedback)
- `Id` (INT, PK, NOT NULL)
- `BezoekerId` (INT, FK -> Bezoeker.Id, NULLABLE)
- `MedewerkerId` (INT, FK -> Medewerker.Id, NULLABLE)
- `Nummer` (MEDIUMINT, NOT NULL)
- `Type` (VARCHAR 20, NOT NULL) - (Notificatie, Klacht, Review)
- `Bericht` (VARCHAR 250, NOT NULL)