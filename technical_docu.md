# CommuCore - Technische Dokumentation

**Hauptmodule: Members, Events & Transactions**

Version 1.0 | Januar 2026

---

## Inhaltsverzeichnis

1. [Einführung](#1-einführung)
2. [Members - Mitgliederverwaltung](#2-members---mitgliederverwaltung)
3. [Events - Veranstaltungsverwaltung](#3-events---veranstaltungsverwaltung)
4. [Transactions - Finanzverwaltung](#4-transactions---finanzverwaltung)
5. [Technische Architektur](#5-technische-architektur)
6. [API & Integration](#6-api--integration)
7. [Installation & Deployment](#7-installation--deployment)
8. [Sicherheit & Datenschutz](#8-sicherheit--datenschutz)
9. [Anpassung & Erweiterung](#9-anpassung--erweiterung)
10. [Testing](#10-testing)
11. [Support & Ressourcen](#11-support--ressourcen)
12. [Anhang](#12-anhang)

---

## 1. Einführung

CommuCore ist eine moderne Open-Source-Plattform zur Verwaltung von Vereinen, Clubs, NGOs und Community-Organisationen. Diese Dokumentation beschreibt die drei zentralen Module der Anwendung:

- **Members** (Mitgliederverwaltung)
- **Events** (Veranstaltungsverwaltung)
- **Transactions** (Finanztransaktionen)

Die Anwendung basiert auf Laravel 12, Livewire 3 und FluxUI und bietet eine vollständig selbst-hostbare Lösung für Organisationen jeder Größe.

---

## 2. Members - Mitgliederverwaltung

### 2.1 Übersicht

Das Members-Modul bildet das Herzstück der Mitgliederverwaltung. Es ermöglicht die zentrale Verwaltung aller Mitglieder, deren Stammdaten, Rollen, Berechtigungen und Mitgliedsstatus.

### 2.2 Datenmodell

#### 2.2.1 Member Entity

Zentrale Entität für Mitgliederdaten mit folgenden Hauptattributen:

| Attribut | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | Integer | Eindeutige Identifikation |
| `first_name` | String | Vorname |
| `last_name` | String | Nachname |
| `email` | String | E-Mail-Adresse (unique) |
| `phone` | String | Telefonnummer |
| `date_of_birth` | Date | Geburtsdatum |
| `member_number` | String | Mitgliedsnummer |
| `membership_type` | Enum/String | Art der Mitgliedschaft |
| `status` | Enum | Status (active, inactive, pending) |
| `joined_at` | DateTime | Beitrittsdatum |
| `address` | Text/JSON | Adressdaten |
| `created_at` | Timestamp | Erstellungszeitpunkt |
| `updated_at` | Timestamp | Letztes Update |

#### 2.2.2 Beziehungen

Typische Relationen des Member-Models:

- **hasMany: Transactions** - Ein Mitglied kann mehrere Transaktionen haben
- **belongsToMany: Events** - Mitglieder können an mehreren Events teilnehmen (Pivot-Tabelle: `event_member`)
- **hasMany: Roles/Permissions** - Rollenzuweisungen über zusätzliche Tabellen

### 2.3 Hauptfunktionen

#### 2.3.1 Mitgliederverwaltung

- **Anlegen neuer Mitglieder**: Erfassung aller relevanten Stammdaten
- **Bearbeiten von Mitgliedsdaten**: Aktualisierung persönlicher Informationen
- **Statusverwaltung**: Aktivierung, Deaktivierung, Pending-Status
- **Mitgliedersuche**: Filterung nach verschiedenen Kriterien (Name, Status, Mitgliedschaftstyp)
- **Export-Funktionen**: CSV, PDF, Excel-Export für externe Nutzung
- **Massenaktionen**: Bulk-Updates für mehrere Mitglieder gleichzeitig

#### 2.3.2 Rollen und Berechtigungen

CommuCore implementiert ein flexibles Rollen- und Berechtigungssystem:

- Rollendefinition (z.B. Admin, Vorstand, Mitglied, Gast)
- Berechtigungszuweisung auf Modul- und Funktionsebene
- Hierarchische Rechtestrukturen
- Dynamische Rollenzuweisung pro Mitglied

### 2.4 Controller & Routes

#### 2.4.1 MemberController

Zentrale Steuerungslogik für Mitglieder-Operationen:

| Methode | Route | HTTP | Beschreibung |
|---------|-------|------|--------------|
| `index()` | `/members` | GET | Liste aller Mitglieder mit Pagination |
| `show($id)` | `/members/{id}` | GET | Detailansicht eines Mitglieds |
| `create()` | `/members/create` | GET | Formular für neues Mitglied |
| `store(Request)` | `/members` | POST | Neues Mitglied speichern |
| `edit($id)` | `/members/{id}/edit` | GET | Bearbeitungsformular |
| `update(Request, $id)` | `/members/{id}` | PUT | Mitglied aktualisieren |
| `destroy($id)` | `/members/{id}` | DELETE | Mitglied löschen/deaktivieren |
| `export()` | `/members/export` | GET | Export (CSV/Excel/PDF) |

#### 2.4.2 Beispiel-Code für Member Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'member_number',
        'membership_type',
        'status',
        'joined_at',
        'address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joined_at' => 'datetime',
        'address' => 'array',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('registration_date', 'status', 'ticket_number')
            ->withTimestamps();
    }

    // Scope für aktive Mitglieder
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessor für Vollständigen Namen
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
```

### 2.5 Livewire-Komponenten

CommuCore nutzt Livewire 3 für reaktive Benutzeroberflächen:

- **`MemberTable.php`**: Interaktive Mitgliederliste mit Echtzeit-Suche, Filter und Sortierung
- **`MemberForm.php`**: Formular für Mitgliederdaten mit Validierung
- **`MemberProfile.php`**: Detailansicht Mitgliederprofil mit Navigation zu Events und Transaktionen
- **`MemberImport.php`**: CSV/Excel Import-Komponente für Massen-Upload

---

## 3. Events - Veranstaltungsverwaltung

### 3.1 Übersicht

Das Events-Modul ermöglicht die Planung, Verwaltung und Durchführung von Veranstaltungen. Es unterstützt Anmeldungen, Ticketing, Aufgabenverwaltung und öffentliche Event-Seiten.

### 3.2 Datenmodell

#### 3.2.1 Event Entity

| Attribut | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | Integer | Eindeutige Identifikation |
| `title` | String | Veranstaltungstitel |
| `description` | Text | Ausführliche Beschreibung |
| `event_type` | Enum/String | Art der Veranstaltung (meeting, workshop, conference) |
| `start_date` | DateTime | Startdatum und -zeit |
| `end_date` | DateTime | Enddatum und -zeit |
| `location` | String/Text | Veranstaltungsort |
| `max_participants` | Integer | Maximale Teilnehmerzahl |
| `registration_deadline` | DateTime | Anmeldefrist |
| `status` | Enum | Status (draft, published, ongoing, completed, cancelled) |
| `price` | Decimal | Teilnahmegebühr |
| `is_public` | Boolean | Öffentlich sichtbar |
| `created_by` | Integer | Ersteller (User-ID) |
| `created_at` | Timestamp | Erstellungszeitpunkt |
| `updated_at` | Timestamp | Letztes Update |

#### 3.2.2 Event Registrations (Pivot-Tabelle)

Verwaltung von Anmeldungen über die `event_member` oder `event_registrations` Tabelle:

| Attribut | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | Integer | Eindeutige Identifikation |
| `event_id` | Foreign Key | Referenz auf Event |
| `member_id` | Foreign Key | Referenz auf Member |
| `registration_date` | DateTime | Anmeldezeitpunkt |
| `status` | Enum | Status (pending, confirmed, attended, cancelled) |
| `ticket_number` | String | Generierte Ticketnummer |
| `notes` | Text | Zusätzliche Notizen |
| `created_at` | Timestamp | Erstellungszeitpunkt |
| `updated_at` | Timestamp | Letztes Update |

#### 3.2.3 Event Tasks

Aufgaben im Zusammenhang mit Events:

| Attribut | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | Integer | Eindeutige Identifikation |
| `event_id` | Foreign Key | Referenz auf Event |
| `title` | String | Aufgabentitel |
| `description` | Text | Aufgabenbeschreibung |
| `assigned_to` | Foreign Key | Zugewiesenes Mitglied |
| `due_date` | DateTime | Fälligkeitsdatum |
| `status` | Enum | Status (todo, in_progress, done) |
| `priority` | Enum | Priorität (low, medium, high) |

### 3.3 Hauptfunktionen

#### 3.3.1 Event-Verwaltung

- **Anlegen von Veranstaltungen**: Erfassung aller Event-Details inkl. Datum, Ort, Beschreibung
- **Bearbeitung und Aktualisierung**: Änderung von Datum, Ort, Details
- **Statusverwaltung**: Draft → Published → Ongoing → Completed/Cancelled
- **Kalenderansicht**: Monats-, Wochen- und Tagesansicht aller Events
- **Recurring Events**: Wiederkehrende Veranstaltungen (wöchentlich, monatlich, jährlich)

#### 3.3.2 Anmeldungsverwaltung

- Online-Anmeldung für Mitglieder und Gäste
- Verwaltung von Wartelisten bei ausgebuchten Events
- Automatische Bestätigungs-E-Mails
- Export von Teilnehmerlisten (CSV, Excel, PDF)
- Anmeldefristen mit automatischer Sperrung
- Self-Service Stornierung für Teilnehmer

#### 3.3.3 Ticketing

- Generierung eindeutiger Ticketnummern
- QR-Code-Erstellung für Check-In
- Ticket-PDF-Download für Teilnehmer
- Check-In-System mit Mobile App oder Scanner
- E-Ticket Versand per E-Mail

#### 3.3.4 Aufgabenverwaltung

- Erstellen von Event-bezogenen Aufgaben
- Zuweisung an Mitglieder
- Deadline-Tracking
- Fortschrittsverfolgung
- E-Mail-Benachrichtigungen bei Fälligkeit

### 3.4 Controller & Routes

#### EventController

| Methode | Route | HTTP | Beschreibung |
|---------|-------|------|--------------|
| `index()` | `/events` | GET | Liste aller Events |
| `show($id)` | `/events/{id}` | GET | Event-Details |
| `create()` | `/events/create` | GET | Formular für neues Event |
| `store(Request)` | `/events` | POST | Event speichern |
| `edit($id)` | `/events/{id}/edit` | GET | Bearbeitungsformular |
| `update(Request, $id)` | `/events/{id}` | PUT | Event aktualisieren |
| `register($id)` | `/events/{id}/register` | POST | Anmeldung zu Event |
| `unregister($id)` | `/events/{id}/unregister` | DELETE | Abmeldung von Event |
| `calendar()` | `/events/calendar` | GET | Kalenderansicht |
| `participants($id)` | `/events/{id}/participants` | GET | Teilnehmerliste |

#### 3.4.2 Beispiel-Code für Event Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'location',
        'max_participants',
        'registration_deadline',
        'status',
        'price',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'price' => 'decimal:2',
        'is_public' => 'boolean',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot('registration_date', 'status', 'ticket_number')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EventTask::class);
    }

    // Scope für kommende Events
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
            ->where('status', 'published')
            ->orderBy('start_date');
    }

    // Prüfung ob Event voll ist
    public function isFullAttribute(): bool
    {
        return $this->members()->count() >= $this->max_participants;
    }

    // Verfügbare Plätze
    public function getAvailableSpotsAttribute(): int
    {
        return max(0, $this->max_participants - $this->members()->count());
    }
}
```

### 3.5 Livewire-Komponenten

- **`EventCalendar.php`**: Interaktive Kalenderansicht mit FullCalendar.js
- **`EventRegistration.php`**: Anmeldeformular für Events
- **`EventParticipants.php`**: Verwaltung und Anzeige von Teilnehmern
- **`EventTaskManager.php`**: Aufgabenverwaltung für Events

---

## 4. Transactions - Finanzverwaltung

### 4.1 Übersicht

Das Transactions-Modul verwaltet alle finanziellen Vorgänge der Organisation, einschließlich Mitgliedsbeiträge, Spenden, Ausgaben und verschiedene Konten (Bank, Kasse, Digital).

### 4.2 Datenmodell

#### 4.2.1 Transaction Entity

| Attribut | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | Integer | Eindeutige Identifikation |
| `transaction_type` | Enum | Art (income, expense, transfer) |
| `category` | String | Kategorie (membership_fee, donation, expense) |
| `amount` | Decimal | Betrag |
| `currency` | String | Währung (EUR, USD, etc.) |
| `transaction_date` | DateTime | Transaktionsdatum |
| `description` | Text | Beschreibung |
| `member_id` | Foreign Key | Zugehöriges Mitglied (optional) |
| `account_id` | Foreign Key | Konto-ID |
| `reference_number` | String | Referenznummer/Belegnummer |
| `status` | Enum | Status (pending, completed, cancelled) |
| `receipt_url` | String | URL zum hochgeladenen Beleg |
| `created_by` | Integer | Ersteller (User-ID) |
| `created_at` | Timestamp | Erstellungszeitpunkt |
| `updated_at` | Timestamp | Letztes Update |

#### 4.2.2 Account Entity

Verwaltung verschiedener Finanzkonten:

| Attribut | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | Integer | Eindeutige Identifikation |
| `account_name` | String | Kontobezeichnung (z.B. "Vereinskonto", "Kasse") |
| `account_type` | Enum | Typ (bank, cash, digital) |
| `account_number` | String | Kontonummer/IBAN |
| `balance` | Decimal | Aktueller Kontostand (berechnet) |
| `currency` | String | Währung |
| `is_active` | Boolean | Aktiv/Inaktiv |
| `description` | Text | Beschreibung |
| `created_at` | Timestamp | Erstellungszeitpunkt |
| `updated_at` | Timestamp | Letztes Update |

### 4.3 Hauptfunktionen

#### 4.3.1 Transaktionsverwaltung

- **Einnahmen erfassen**: Mitgliedsbeiträge, Spenden, Veranstaltungseinnahmen
- **Ausgaben erfassen**: Rechnungen, Kosten, Material, Gehälter
- **Überweisungen**: Transfers zwischen verschiedenen Konten
- **Belegverwaltung**: Upload und Verwaltung von Belegen/Quittungen (PDF, Bilder)
- **Kategorisierung**: Flexible Kategorien für bessere Übersicht
- **Massenimport**: CSV-Import von Banktransaktionen

#### 4.3.2 Kontoverwaltung

- Verwaltung mehrerer Konten (Bankkonto, Vereinskasse, PayPal, etc.)
- Automatische Kontostandsberechnung basierend auf Transaktionen
- Kontoabstimmung (Reconciliation)
- Übersicht über alle Kontobewegungen
- Multi-Währungsunterstützung

#### 4.3.3 Reporting & Auswertungen

- **Finanzberichte**:
    - Einnahmen-Ausgaben-Rechnung (GuV)
    - Bilanzübersicht
    - Cashflow-Analyse
- **Kategorienauswertung**: Detaillierte Auswertung nach Kategorien
- **Mitgliedsbeiträge**: Übersicht über Zahlungen und offene Beiträge pro Mitglied
- **Zeitraumvergleiche**: Monats-, Quartals-, Jahresvergleiche
- **Export**: CSV, PDF, Excel für externe Buchhaltungssoftware
- **Dashboard**: Visuelle Darstellung wichtiger Finanzkennzahlen

#### 4.3.4 Mitgliedsbeiträge

- Automatische Generierung von Beitragsrechnungen
- Tracking von Zahlungseingängen
- Mahnwesen für überfällige Beiträge
- Verschiedene Beitragsstufen (Vollmitglied, Ermäßigt, Ehrenmitglied)

### 4.4 Controller & Routes

#### TransactionController

| Methode | Route | HTTP | Beschreibung |
|---------|-------|------|--------------|
| `index()` | `/transactions` | GET | Liste aller Transaktionen |
| `show($id)` | `/transactions/{id}` | GET | Transaktionsdetails |
| `create()` | `/transactions/create` | GET | Neue Transaktion erfassen |
| `store(Request)` | `/transactions` | POST | Transaktion speichern |
| `edit($id)` | `/transactions/{id}/edit` | GET | Transaktion bearbeiten |
| `update(Request, $id)` | `/transactions/{id}` | PUT | Transaktion aktualisieren |
| `destroy($id)` | `/transactions/{id}` | DELETE | Transaktion löschen |
| `reports()` | `/transactions/reports` | GET | Finanzberichte |
| `export()` | `/transactions/export` | GET | Export (CSV/Excel/PDF) |

#### AccountController

| Methode | Route | HTTP | Beschreibung |
|---------|-------|------|--------------|
| `index()` | `/accounts` | GET | Liste aller Konten |
| `show($id)` | `/accounts/{id}` | GET | Kontodetails |
| `balance($id)` | `/accounts/{id}/balance` | GET | Aktueller Kontostand |
| `transactions($id)` | `/accounts/{id}/transactions` | GET | Kontobewegungen |

#### 4.4.2 Beispiel-Code für Transaction Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_type',
        'category',
        'amount',
        'currency',
        'transaction_date',
        'description',
        'member_id',
        'account_id',
        'reference_number',
        'status',
        'receipt_url',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // Scope für Einnahmen
    public function scopeIncome($query)
    {
        return $query->where('transaction_type', 'income');
    }

    // Scope für Ausgaben
    public function scopeExpense($query)
    {
        return $query->where('transaction_type', 'expense');
    }

    // Scope für Zeitraum
    public function scopeInPeriod($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }
}
```

### 4.5 Livewire-Komponenten

- **`TransactionTable.php`**: Liste aller Transaktionen mit Filterung
- **`TransactionForm.php`**: Formular für neue Transaktionen
- **`FinancialDashboard.php`**: Dashboard mit Finanzkennzahlen und Charts
- **`AccountOverview.php`**: Kontenübersicht mit aktuellen Ständen
- **`MembershipFeeManager.php`**: Verwaltung von Mitgliedsbeiträgen

---

## 5. Technische Architektur

### 5.1 Technology Stack

| Komponente | Technologie | Version |
|------------|-------------|---------|
| Framework | Laravel | 12.x |
| Frontend Framework | Livewire | 3.x |
| UI Components | FluxUI | Latest |
| Datenbank | MySQL/PostgreSQL | 8.0+ / 14+ |
| PHP Version | PHP | 8.3+ |
| Package Manager | Composer | 2.x |
| Asset Bundler | Vite | Latest |
| CSS Framework | Tailwind CSS | Latest |

### 5.2 Ordnerstruktur

Wichtige Verzeichnisse der Laravel-Anwendung:

```
commucore/
├── app/
│   ├── Models/              # Eloquent Models (Member, Event, Transaction, etc.)
│   ├── Http/
│   │   ├── Controllers/     # Controller-Klassen
│   │   └── Middleware/      # Custom Middleware
│   ├── Livewire/            # Livewire-Komponenten
│   └── Services/            # Business Logic Services
├── database/
│   ├── migrations/          # Datenbankmigrationen
│   ├── seeders/             # Seeder für Testdaten
│   └── factories/           # Model Factories
├── resources/
│   ├── views/               # Blade Templates
│   │   ├── members/
│   │   ├── events/
│   │   └── transactions/
│   └── js/                  # JavaScript Assets
├── routes/
│   ├── web.php              # Web-Routen
│   └── api.php              # API-Routen
├── config/                  # Konfigurationsdateien
├── public/                  # Öffentliche Assets
└── tests/                   # Test-Suite
```

### 5.3 Datenbankschema

#### 5.3.1 Haupttabellen

- **`members`** - Mitgliederdaten
- **`events`** - Veranstaltungen
- **`transactions`** - Finanztransaktionen
- **`accounts`** - Finanzkonten

#### 5.3.2 Beziehungstabellen

- **`event_member`** - Many-to-Many zwischen Events und Members
- **`event_tasks`** - Aufgaben zu Events
- **`roles`** - Rollendefinitionen
- **`permissions`** - Berechtigungen
- **`role_user`** - Zuweisung Rollen zu Users

#### 5.3.3 ER-Diagramm (vereinfacht)

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│   Member    │────────<│ event_member│>────────│    Event    │
└─────────────┘         └─────────────┘         └─────────────┘
      │                                                 │
      │                                                 │
      │ 1:N                                          1:N│
      │                                                 │
      ▼                                                 ▼
┌─────────────┐                               ┌─────────────┐
│ Transaction │                               │ EventTask   │
└─────────────┘                               └─────────────┘
      │
      │ N:1
      │
      ▼
┌─────────────┐
│   Account   │
└─────────────┘
```

### 5.4 Design Patterns

CommuCore folgt bewährten Design Patterns:

- **MVC (Model-View-Controller)**: Trennung von Datenlogik, Präsentation und Steuerung
- **Repository Pattern**: Abstraktion der Datenzugriffslogik
- **Service Layer**: Business Logic außerhalb der Controller
- **Observer Pattern**: Event-basierte Kommunikation (Laravel Events)
- **Factory Pattern**: Model Factories für Tests

---

## 6. API & Integration

### 6.1 REST API Endpoints

CommuCore bietet optionale API-Endpoints für externe Integrationen:

#### 6.1.1 Members API

| Endpoint | Methode | Beschreibung |
|----------|---------|--------------|
| `/api/members` | GET | Liste aller Mitglieder (paginiert) |
| `/api/members/{id}` | GET | Mitglied abrufen |
| `/api/members` | POST | Neues Mitglied erstellen |
| `/api/members/{id}` | PUT | Mitglied aktualisieren |
| `/api/members/{id}` | DELETE | Mitglied löschen |
| `/api/members/search` | GET | Mitgliedersuche |

#### 6.1.2 Events API

| Endpoint | Methode | Beschreibung |
|----------|---------|--------------|
| `/api/events` | GET | Liste aller Events (paginiert) |
| `/api/events/{id}` | GET | Event abrufen |
| `/api/events/{id}/register` | POST | Anmeldung zu Event |
| `/api/events/{id}/unregister` | DELETE | Abmeldung von Event |
| `/api/events/{id}/participants` | GET | Teilnehmerliste |
| `/api/events/calendar` | GET | Calendar Feed (iCal) |

#### 6.1.3 Transactions API

| Endpoint | Methode | Beschreibung |
|----------|---------|--------------|
| `/api/transactions` | GET | Liste aller Transaktionen |
| `/api/transactions/{id}` | GET | Transaktion abrufen |
| `/api/transactions` | POST | Neue Transaktion |
| `/api/transactions/{id}` | PUT | Transaktion aktualisieren |
| `/api/accounts/{id}/balance` | GET | Kontostand abfragen |
| `/api/reports/financial` | GET | Finanzbericht abrufen |

### 6.2 Authentifizierung

Die API verwendet Token-basierte Authentifizierung mit Laravel Sanctum:

- **Bearer Token** im Authorization Header
- Token-Generierung über `/api/login` Endpoint
- **Rate Limiting**: 60 Requests pro Minute pro IP
- **Scopes**: Verschiedene Berechtigungsstufen für API-Tokens

**Beispiel API-Request:**

```bash
curl -X GET https://your-commucore.com/api/members \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```

### 6.3 Webhooks

CommuCore kann Webhooks für folgende Events senden:

- Neue Mitgliederregistrierung
- Event-Anmeldung
- Neue Transaktion
- Statusänderungen

### 6.4 Export-Formate

Unterstützte Export-Formate:

- **CSV**: Für Excel/Spreadsheet-Import
- **Excel (XLSX)**: Native Excel-Dateien
- **PDF**: Formatierte Reports
- **JSON**: API-Export
- **iCal**: Kalender-Feed für Events

---

## 7. Installation & Deployment

### 7.1 Systemanforderungen

| Komponente | Minimum | Empfohlen |
|------------|---------|-----------|
| PHP | 8.3 | 8.3 oder höher |
| Datenbank | MySQL 8.0 / PostgreSQL 14 | MySQL 8.0+ / PostgreSQL 15+ |
| Webserver | Apache 2.4 / Nginx 1.18 | Nginx 1.24+ |
| RAM | 512 MB | 1 GB+ |
| Festplatte | 500 MB | 2 GB+ |
| Node.js | 18.x | 20.x+ |
| Composer | 2.x | Latest |

### 7.2 Installationsschritte

Detaillierte Anweisungen finden Sie in der `INSTALL.md` Datei. Kurzübersicht:

1. **Repository klonen**
   ```bash
   git clone https://github.com/d-siteware/commucore.git
   cd commucore
   ```

2. **Abhängigkeiten installieren**
   ```bash
   composer install
   npm install
   ```

3. **Umgebungsdatei konfigurieren**
   ```bash
   cp .env.example .env
   # .env editieren und Datenbankverbindung konfigurieren
   ```

4. **Anwendungsschlüssel generieren**
   ```bash
   php artisan key:generate
   ```

5. **Datenbank migrieren**
   ```bash
   php artisan migrate
   ```

6. **Seeder ausführen (optional)**
   ```bash
   php artisan db:seed
   ```

7. **Assets kompilieren**
   ```bash
   npm run build
   ```

8. **Webserver konfigurieren**
    - DocumentRoot auf `/public` setzen
    - URL Rewriting aktivieren

9. **Anwendung starten**
   ```bash
   php artisan serve
   ```

### 7.3 Produktiv-Deployment

Für Produktivumgebungen:

- **Queue Worker** starten: `php artisan queue:work`
- **Task Scheduler** konfigurieren: Cron-Job für `php artisan schedule:run`
- **Cache optimieren**: `php artisan optimize`
- **SSL/TLS** konfigurieren
- **Backup-Strategie** implementieren

### 7.4 Docker Deployment

CommuCore kann auch mit Docker deployed werden:

```bash
docker-compose up -d
```

---

## 8. Sicherheit & Datenschutz

### 8.1 Sicherheitsfeatures

- **CSRF-Protection**: Laravel CSRF-Token auf allen Formularen
- **XSS-Prevention**: Automatisches Escaping in Blade Templates
- **SQL Injection Prevention**: Eloquent ORM mit Prepared Statements
- **Password Hashing**: BCrypt-Hashing für Passwörter (min. 8 Zeichen)
- **HTTPS**: Erzwungene SSL/TLS-Verbindung in Produktion
- **Rate Limiting**: Schutz vor Brute-Force-Angriffen
- **Two-Factor Authentication**: Optional aktivierbar
- **Session Security**: Secure Cookies, HTTP-Only

### 8.2 Datenschutz (DSGVO-konform)

- **Datensparsamkeit**: Nur notwendige Daten werden erhoben
- **Recht auf Löschung**: Mitgliederdaten können vollständig gelöscht werden
- **Recht auf Datenportabilität**: Export persönlicher Daten als JSON/CSV
- **Einwilligungsverwaltung**: Tracking von Datenschutz-Einwilligungen
- **Audit Logs**: Protokollierung von Änderungen an sensiblen Daten
- **Zugriffskontrollen**: Rollenbasierte Berechtigungen
- **Datenverschlüsselung**: Sensible Daten verschlüsselt in Datenbank

### 8.3 Best Practices

- Regelmäßige Updates der Abhängigkeiten
- Security Headers konfigurieren
- Regelmäßige Backups
- Monitoring und Logging
- Penetration Testing

---

## 9. Anpassung & Erweiterung

### 9.1 Konfiguration

Zentrale Konfigurationsdateien in `config/`:

- **`app.php`**: Grundlegende Anwendungseinstellungen
- **`database.php`**: Datenbankverbindungen
- **`mail.php`**: E-Mail-Konfiguration (SMTP, Mailgun, etc.)
- **`filesystems.php`**: Dateispeicher-Konfiguration
- **`services.php`**: Externe Service-Integrationen

### 9.2 Mehrsprachigkeit

CommuCore unterstützt mehrere Sprachen out-of-the-box:

- Sprachdateien im `lang/` Verzeichnis
- Aktuell verfügbar: **Deutsch (de)**, **Englisch (en)**, **Ungarisch (hu)**
- Einfaches Hinzufügen neuer Sprachen durch Kopieren und Übersetzen
- Frontend und Backend vollständig übersetzbar
- Sprachwahl pro User

**Neue Sprache hinzufügen:**

```bash
# Sprachverzeichnis erstellen
mkdir lang/fr

# Dateien aus einer existierenden Sprache kopieren
cp -r lang/en/* lang/fr/

# Dateien übersetzen
```

### 9.3 Custom Modules & Erweiterungen

Die modulare Architektur ermöglicht einfache Erweiterungen:

1. **Neue Models** in `app/Models/`
2. **Neue Controller** in `app/Http/Controllers/`
3. **Neue Livewire-Komponenten** in `app/Livewire/`
4. **Routen registrieren** in `routes/web.php`
5. **Migrationen** in `database/migrations/`

**Beispiel: Newsletter-Modul hinzufügen**

```bash
# Model erstellen
php artisan make:model Newsletter -m

# Controller erstellen
php artisan make:controller NewsletterController

# Livewire Component erstellen
php artisan make:livewire NewsletterSubscription
```

### 9.4 Theming & Styling

- **Tailwind CSS** für alle Styles
- **FluxUI Components** für konsistente UI
- Custom CSS in `resources/css/app.css`
- Logo und Branding anpassbar in `config/app.php`

---

## 10. Testing

### 10.1 Test-Suite

CommuCore enthält eine umfassende Test-Suite basierend auf PHPUnit:

- **Unit Tests**: Tests für einzelne Klassen und Methoden
- **Feature Tests**: End-to-End-Tests für komplette Features
- **Browser Tests**: Laravel Dusk für UI-Tests (optional)

Test-Dateien befinden sich in `tests/`:

```
tests/
├── Unit/
│   ├── MemberTest.php
│   ├── EventTest.php
│   └── TransactionTest.php
├── Feature/
│   ├── MemberManagementTest.php
│   ├── EventRegistrationTest.php
│   └── TransactionFlowTest.php
└── Browser/
    └── EventCalendarTest.php
```

### 10.2 Tests ausführen

Kommandos zum Ausführen der Tests:

```bash
# Alle Tests ausführen
php artisan test

# Spezifische Test-Datei
php artisan test tests/Feature/MemberManagementTest.php

# Mit Code Coverage
php artisan test --coverage

# Nur Unit Tests
php artisan test --testsuite=Unit

# Nur Feature Tests
php artisan test --testsuite=Feature
```

### 10.3 Continuous Integration

CommuCore ist vorbereitet für CI/CD-Pipelines:

- **GitHub Actions** Workflow in `.github/workflows/`
- Automatische Tests bei jedem Push/Pull Request
- Code Quality Checks (PHPStan, Pint)

---

## 11. Support & Ressourcen

### 11.1 Dokumentation

- **README.md**: Projekt-Übersicht und Features
- **INSTALL.md**: Detaillierte Installationsanleitung
- **CONTRIBUTING.md**: Richtlinien für Beiträge
- **ROADMAP.md**: Geplante Funktionen und Entwicklung
- **TRANSLATIONS.md**: Anleitung für Übersetzungen
- **BRAND.md**: Branding Guidelines

### 11.2 Community & Support

- **GitHub Repository**: [https://github.com/d-siteware/commucore](https://github.com/d-siteware/commucore)
- **Issue Tracker**: Bug Reports und Feature Requests auf GitHub
- **Discussions**: Community-Forum auf GitHub Discussions
- **Website**: [https://commu-core.org](https://commu-core.org)
- **Demo**: [https://commucore.app](https://commucore.app)

### 11.3 Professioneller Support

Für professionelle Dienstleistungen:

- **Installation & Setup**
- **Customization & Anpassungen**
- **Hosting & Wartung**
- **Schulungen & Workshops**
- **Prioritäts-Support**

Kontakt: [https://commu-core.com](https://commu-core.com) oder [d-siteware.io](https://d-siteware.io)

### 11.4 Lizenz

CommuCore ist Open-Source-Software unter der **GNU General Public License v3.0 (GPL-3.0)**.

Sie können die Software frei:
- Nutzen (für jeden Zweck)
- Studieren und ändern (Quellcode ist verfügbar)
- Weitergeben (Kopien verteilen)
- Verbessern und Verbesserungen veröffentlichen

**Bedingungen:**
- Abgeleitete Werke müssen ebenfalls GPL-3.0 lizenziert sein
- Quellcode muss verfügbar gemacht werden
- Änderungen müssen dokumentiert werden

Vollständige Lizenz: [LICENSE](LICENSE) Datei im Repository

---

## 12. Anhang

### 12.1 Glossar

| Begriff | Beschreibung |
|---------|--------------|
| **Eloquent** | Laravel's ORM (Object-Relational Mapping) für Datenbankzugriff |
| **Livewire** | Full-stack Framework für dynamische UI-Komponenten ohne JavaScript |
| **FluxUI** | UI-Komponenten-Bibliothek für Laravel/Livewire |
| **Blade** | Template-Engine von Laravel |
| **Migration** | Versionskontrolle für Datenbankschema |
| **Seeder** | Klasse zum Befüllen der Datenbank mit Testdaten |
| **Middleware** | Filter für HTTP-Requests |
| **Artisan** | Command-Line Interface von Laravel |
| **Sanctum** | Laravel-Paket für API-Authentifizierung |
| **Pivot Table** | Zwischentabelle für Many-to-Many Beziehungen |

### 12.2 Häufige Probleme (FAQ)

**Q: Wie kann ich die Standard-Sprache ändern?**
A: In der `.env` Datei `APP_LOCALE=de` auf die gewünschte Sprache setzen.

**Q: Wie füge ich einen neuen Admin-User hinzu?**
A: Mit dem Artisan-Befehl: `php artisan make:admin [email]`

**Q: Wie kann ich Testdaten generieren?**
A: Mit Seedern: `php artisan db:seed`

**Q: Welche E-Mail-Provider werden unterstützt?**
A: SMTP, Mailgun, SendGrid, Amazon SES, etc. - konfigurierbar in `config/mail.php`

**Q: Kann ich CommuCore mit meiner bestehenden Datenbank verbinden?**
A: Ja, passen Sie die Verbindungsdetails in `.env` an und führen Sie die Migrationen aus.

### 12.3 Changelog

Versionsverlauf und Änderungen werden im GitHub Repository gepflegt:
- [https://github.com/d-siteware/commucore/releases](https://github.com/d-siteware/commucore/releases)

### 12.4 Mitwirkende

CommuCore wird entwickelt und gepflegt von **d-siteware.io**.

Vielen Dank an alle Contributors, die zu diesem Projekt beitragen!

Eine vollständige Liste der Contributors finden Sie auf GitHub:
- [https://github.com/d-siteware/commucore/graphs/contributors](https://github.com/d-siteware/commucore/graphs/contributors)

### 12.5 Roadmap-Highlights

Geplante Features für kommende Versionen:

- **Mobile App** (iOS/Android) für Mitglieder
- **Advanced Reporting** mit erweiterten Dashboards
- **Plugin-System** für modulare Erweiterungen
- **Multi-Tenancy** für mehrere Organisationen in einer Installation
- **Integration** mit gängigen Accounting-Software
- **Newsletter-Modul** mit E-Mail-Kampagnen
- **Document Management** für Dateiverwaltung
- **Forum/Community** Funktionen

Vollständige Roadmap: [ROADMAP.md](ROADMAP.md)

---

## Danksagung

CommuCore steht auf den Schultern von Riesen:

- Laravel Team für das fantastische Framework
- Livewire Team für die reaktive UI-Library
- FluxUI für die wunderschönen UI-Komponenten
- Open-Source Community für unzählige Pakete und Tools

---

**© 2026 d-siteware.io | CommuCore**

*Diese Dokumentation wurde erstellt am 29. Januar 2026*
*Version 1.0*