# Mitglieder-Export

Der Export ermöglicht das Herunterladen von Mitgliederdaten in drei Modi. Der Zugriff ist auf Admins und aktive Vorstandsmitglieder beschränkt.

---

## Export-Typen

| Typ | Dateiformat | Inhalt |
|---|---|---|
| **Stammdaten** | CSV | Name, Kontaktdaten, Adresse, Geschlecht |
| **Alle Felder** | CSV | Alle Mitgliederdaten inkl. Rollen, Beitragstyp, DSGVO-Felder |
| **Vollexport** | ZIP | Alle Felder + Mitgliedsdokumente + Checksum-Manifest |

---

## Voraussetzungen

- Benutzer muss Admin oder aktives Vorstandsmitglied sein (`MemberType::MD`, kein `left_at`, nicht pseudonymisiert)
- Der Export wird in der Audit-History protokolliert (`Log::info('member.export', [...])`)

---

## Export-Formular

Das Export-Formular ist unter `backend/members/export` erreichbar und bietet folgende Filter:

### Export-Typ

Auswahl des Export-Typs über Radio-Buttons (Cards-Variante):

- **Stammdaten** – Nur Basiskontaktdaten
- **Alle Felder** – Vollständige Mitgliedsdaten
- **Vollexport (ZIP)** – Alle Felder + Dokumente

### Filter

| Filter | Beschreibung | Standard |
|---|---|---|
| Nur aktive Mitglieder | Filtert auf Mitglieder ohne `left_at` | aus |
| Pseudonymisierte einschließen | Schließt pseudonymisierte Mitglieder ein | aus |
| Mitgliedertypen | Mehrfachauswahl der Mitgliedertypen | alle |

### Live-Vorschau

Die Anzahl der gefundenen Mitglieder wird live aktualisiert während die Filter geändert werden. Der Download-Button ist deaktiviert wenn keine Mitglieder gefunden werden.

---

## CSV-Format

- **Zeichenkodierung**: UTF-8 mit BOM (`\xEF\xBB\xBF`) für Excel-Kompatibilität
- **Trennzeichen**: Semikolon (`;`)
- **Datumsformat**: `YYYY-MM-DD`
- **Boolean**: `ja` / `nein`

### Spalten Stammdaten

| Spalte | Beschreibung |
|---|---|
| Name | Nachname |
| Vorname | Vorname |
| E-Mail | E-Mail-Adresse |
| Telefon | Telefonnummer |
| Mobil | Mobilnummer |
| Adresse | Straße und Hausnummer |
| PLZ | Postleitzahl |
| Ort | Stadt |
| Land | Land |
| Sprache | Sprachcode (z.B. `de`) |
| Geschlecht | Enum-Wert |

### Zusätzliche Spalten (Alle Felder)

| Spalte | Beschreibung |
|---|---|
| Geburtsdatum | Datum |
| Geburtsort | Freitext |
| Staatsangehörigkeit | Freitext |
| Familienstand | Enum-Wert |
| Typ | Mitgliedertyp (Enum) |
| Beitragstyp | Enum-Wert |
| Eingetreten | Eintrittsdatum |
| Ausgetreten | Austrittsdatum |
| Beantragt | Antragsdatum |
| E-Mail-Bestätigung | Verifizierungsdatum |
| Beitragsbefreiung | `ja` / `nein` |
| Befreiungsgrund | Freitext |
| Rollen | Kommaseparierte aktive Rollen |
| Foto-Zustimmung | Datum der Einwilligung |
| Foto-Absage | Datum des Widerrufs |
| Newsletter-Zustimmung | Datum der Einwilligung |
| Newsletter-Absage | Datum des Widerrufs |
| DSGVO-Zustimmung | Datum der Einwilligung |
| Pseudonymisiert | Datum der Pseudonymisierung |

---

## ZIP-Format (Vollexport)

Das ZIP-Archiv hat folgende Struktur:

```
export.zip
├── commucore_export.json     ← Manifest mit Checksum
├── members_all.csv           ← Alle Mitgliedsdaten
└── documents/
    ├── 1_mustermann/
    │   ├── antrag.pdf
    │   └── sepa_mandat.pdf
    └── 2_schmidt/
        └── ausweiskopie.jpg
```

### Manifest (`commucore_export.json`)

```json
{
    "version": "1.0",
    "app": "commucore",
    "exported_at": "2026-03-04T14:00:00+00:00",
    "export_type": "full",
    "member_count": 42,
    "checksums": {
        "members_all.csv": "sha256:abc123..."
    }
}
```

Das Manifest enthält einen SHA256-Hash der CSV-Datei. Beim Re-Import wird dieser Hash geprüft um sicherzustellen dass die Datei aus CommuCore stammt und nicht manipuliert wurde.

---

## Berechtigungen

Der Export ist über die `MemberExportPolicy` abgesichert:

```php
// Erlaubt: Admins
// Erlaubt: Aktive Vorstandsmitglieder (MemberType::MD, kein left_at, nicht pseudonymisiert)
// Verweigert: Alle anderen
```

Im Blade: `@can('export', Member::class)`  
Im Controller: `Gate::authorize('export', Member::class)`

---

## Audit-Log

Jeder Export wird protokolliert:

```php
Log::info('member.export', [
    'user_id'     => auth()->id(),
    'export_type' => $exportType,
    'filters'     => $filters,
    'count'       => $memberCount,
]);
```

---

## Technische Details

### Architektur

```
MemberExportController   → Routen-Handler, streamt Response
MemberExportQuery        → Filter-Builder (Builder<Member>)
MemberCsvExporter        → Generiert CSV-Stream
MemberFullExporter       → Erstellt ZIP-Archiv
MemberExportPolicy       → Zugriffsschutz
```

### Source of Truth

Die Spaltenreihenfolge und -bezeichnungen sind in `MemberFieldMapper::MEMBER_FIELDS` definiert und werden sowohl vom Exporter als auch vom Importer verwendet:

```php
// Services/Import/MemberFieldMapper.php
public const MEMBER_FIELDS = [
    'name'       => 'Name',
    'first_name' => 'Vorname',
    'email'      => 'E-Mail',
    // …
];
```

Änderungen an den Labels müssen nur an dieser Stelle vorgenommen werden.

### Speicher

- **CSV**: Wird direkt gestreamt (`StreamedResponse`) – kein temporärer Speicher
- **ZIP**: Wird in eine temporäre Datei geschrieben, gestreamt, dann gelöscht (`@unlink`)

---

## Routen

| Route | Name | Beschreibung |
|---|---|---|
| `GET /backend/members/export` | `backend.members.export` | Export-Formular |
| `GET /backend/members/export/download` | `backend.members.export.download` | Export herunterladen |

---

## Zusammenspiel mit Import

Export und Import sind aufeinander abgestimmt:

```
Export (CSV)  →  Import (CSV)  →  Auto-Mapping aller Felder
Export (ZIP)  →  Import (ZIP)  →  Checksum-Prüfung + Auto-Mapping + Dokumente
```

Ein Export kann direkt als Import-Vorlage verwendet werden. Die Spaltenüberschriften sind identisch mit den Bezeichnungen im Import-Assistenten.