# Mitglieder-Import

Der Import-Assistent ermöglicht das Einlesen von Mitgliederdaten aus CSV- oder ZIP-Dateien in drei Modi.

---

## Import-Typen

| Typ | Dateiformat | Inhalt |
|---|---|---|
| **Stammdaten** | CSV | Name, Kontaktdaten, Adresse, Geschlecht |
| **Alle Felder** | CSV | Alle Mitgliederdaten inkl. Rollen, Beitragstyp, DSGVO-Felder |
| **Vollexport** | ZIP | Alle Felder + Mitgliedsdokumente |

---

## Voraussetzungen

- Benutzer muss Admin oder aktives Vorstandsmitglied sein (`MemberType::MD`)
- CSV-Dateien müssen UTF-8 kodiert sein (BOM wird automatisch entfernt)
- Trennzeichen wird automatisch erkannt (`;` oder `,`)
- ZIP-Dateien müssen aus CommuCore exportiert worden sein (Checksum-Prüfung)

---

## Der Import-Assistent

Der Assistent führt in 4 Schritten durch den Import:

### Schritt 1 – Upload

Datei auswählen und Import-Typ festlegen. Die Datei wird per Drag & Drop oder Klick hochgeladen.

- **CSV**: Wird sofort eingelesen und analysiert
- **ZIP**: Wird auf Echtheit geprüft (Checksum), dann als Hintergrund-Job verarbeitet

> **ZIP-Import:** Da ZIP-Dateien Dokumente enthalten können, wird die Verarbeitung als Queue-Job ausgeführt. Nach Abschluss erhält der importierende Benutzer eine E-Mail-Benachrichtigung.

### Schritt 2 – Felderzuordnung

Die Spalten der CSV-Datei werden den CommuCore-Feldern zugeordnet.

- **Automatische Zuordnung**: Spalten die exakt mit CommuCore-Feldnamen übereinstimmen werden automatisch zugeordnet
- **Manuelle Zuordnung**: Unbekannte Spalten können manuell zugeordnet oder ignoriert werden
- **Bereits zugewiesene Felder** werden in anderen Dropdowns deaktiviert (keine Doppelbelegung)

Werden in den Daten unbekannte Enum-Werte gefunden (z.B. unbekannte Mitgliedertypen), öffnet sich ein Modal zur manuellen Zuordnung.

### Schritt 3 – Vorschau & Backup

Vor dem Import wird eine Vorschau der ersten 10 Zeilen angezeigt:

- **Grün**: Neues Mitglied
- **Gelb**: Duplikat – wird beim Import übersprungen

Duplikate werden anhand der E-Mail-Adresse erkannt. Zeilen ohne E-Mail werden immer importiert.

**Backup:** Vor dem Import wird automatisch ein JSON-Backup der aktuellen Mitgliederdaten erstellt. Der Download-Link ist 24 Stunden gültig.

### Schritt 4 – Import

Der Import wird mit einer Bestätigungsabfrage gestartet. Nach Abschluss wird ein Protokoll angezeigt:

| Feld | Bedeutung |
|---|---|
| Importiert | Neu angelegte Mitglieder |
| Übersprungen | Duplikate (gleiche E-Mail bereits vorhanden) |
| Fehler | Zeilen mit fehlenden Pflichtfeldern oder ungültigen Werten |
| Dauer | Verarbeitungsdauer in Millisekunden |

Nach dem Import wird eine E-Mail mit dem Protokoll und dem Backup-Download-Link verschickt.

---

## Rollback

Der **Rollback-Button** ist 24 Stunden nach dem Import verfügbar. Er stellt den Zustand vor dem Import wieder her:

1. Alle importierten Mitglieder werden gelöscht
2. Die gesicherten Mitglieder und Rollen aus dem Backup werden wiederhergestellt

> **Achtung:** Der Rollback löscht alle nach dem Import manuell angelegten Mitglieder ebenfalls. Er sollte nur bei fehlerhaften Importen verwendet werden.

---

## CSV-Vorlage

Auf der Import-Seite steht eine leere CSV-Vorlage zum Download bereit. Sie enthält die korrekten Spaltenüberschriften für den gewählten Import-Typ und kann direkt in Excel oder einem anderen Tabellenkalkulationsprogramm befüllt werden.

---

## Duplikat-Behandlung

Duplikate werden anhand der **E-Mail-Adresse** erkannt:

- Existiert ein Mitglied mit der gleichen E-Mail → Zeile wird übersprungen
- Mehrere Zeilen in der gleichen Import-Datei mit gleicher E-Mail → nur die erste wird importiert
- Zeilen ohne E-Mail → werden immer importiert (kein Duplikatcheck möglich)

---

## Unterstützte Felder

### Stammdaten

| CSV-Spalte | Feld | Pflichtfeld  |
|---|---|:------------:|
| Name | `name` |      ✅       |
| Vorname | `first_name` |              |
| E-Mail | `email` |              |
| Telefon | `phone` |              |
| Mobil | `mobile` |              |
| Adresse | `address` |              |
| PLZ | `zip` |              |
| Ort | `city` |              |
| Land | `country` |              |
| Sprache | `locale` |              |
| Geschlecht | `gender` |              |

### Personendaten (zusätzlich bei „Alle Felder")

Wenn `Alle Felder` ausgewählt ist werden noch weitere Felder importiert:

#### Zusätzliche Personendaten

| CSV-Spalte          | Feld          | Mögliche Werte      |
|---------------------|---------------|---------------------|
| Geschlecht          | gender        | `male`, `female`, ... |
| Geburtsdatum        | birth_date    | Datum (YYYY-MM-DD)  |
| Geburtsort          | birth_place   | Freitext            |
| Staatsangehörigkeit | citizenship   | Freitext            |
| Familienstand       | family_status | Freitext            |

#### Mitgliedschaft

| CSV-Spalte        | Feld               | Mögliche Werte [Vorgabe]            | Pflichtfeld |
|-------------------|--------------------|-------------------------------------|:-----------:|
| Typ               | `type`             | `standard`, `board`, `honorary`, …  |             |
| Beitragstyp       | `fee_type`         | `full`, `reduced`, `free`   [`full`] |      ✅      |
| Eingetreten       | `entered_at`       | Datum (YYYY-MM-DD)                  |             |
| Ausgetreten       | `left_at`          | Datum (YYYY-MM-DD)                  |             |
| Beantragt         | `applied_at`       | Datum (YYYY-MM-DD)                  |      ✅      |
| Beitragsbefreiung | `is_deducted`      | `ja` / `nein`     [`nein`]          |      ✅      |
| Befreiungsgrund   | `deduction_reason` | Freitext                            |             |

#### Spalten zur DSGVO-Zustimmungen / Absagen

| CSV-Spalte | Feld | Mögliche Werte |
|---|---|---|
| Foto-Zustimmung | `photo_consent_at` | Datum (YYYY-MM-DD) |
| Foto-Absage | `photo_consent_revoked_at` | Datum (YYYY-MM-DD) |
| Newsletter-Zustimmung | `newsletter_consent_at` | Datum (YYYY-MM-DD) |
| Newsletter-Absage | `newsletter_consent_revoked_at` | Datum (YYYY-MM-DD) |
| DSGVO-Zustimmung | `gdpr_consent_at` | Datum (YYYY-MM-DD) |
| Pseudonymisiert | `pseudonymized_at` | Datum (YYYY-MM-DD) |


---

## Technische Details

### Architektur

```
UploadStep      → CSV/ZIP einlesen, in Session speichern
MappingStep     → Felder zuordnen, Enum-Werte mappen
PreviewStep     → Duplikate erkennen, Backup erstellen
ImportStep      → Import ausführen, Protokoll anzeigen
```

### Session-Keys

| Key | Inhalt |
|---|---|
| `import_csv_headers` | Spaltenüberschriften der CSV |
| `import_all_rows` | Alle Zeilen der CSV (raw) |
| `import_mapped_rows` | Zeilen nach Felderzuordnung |
| `import_total_rows` | Gesamtanzahl der Zeilen |
| `import_document_map` | Dokument-Zuordnung (nur ZIP) |
| `import_extract_dir` | Temporäres Verzeichnis (nur ZIP) |

### Backup-Speicherort

Backups werden unter `storage/app/imports/` gespeichert:

```
imports/
  backup_2026-03-04_143000_uuid.json
  zip/
    import_2026-03-04_143000_uuid.zip
```

### Queue-Job (ZIP)

Der `ProcessMemberZipImport`-Job verarbeitet ZIP-Importe asynchron:

- Timeout: 10 Minuten
- Versuche: 1 (kein Retry bei Fehler)
- Bei Erfolg: `MemberImportCompleted` Mail
- Bei Fehler: `MemberImportFailed` Mail + ZIP-Cleanup

---

## Routen

| Route | Name | Beschreibung |
|---|---|---|
| `GET /backend/members/import` | `backend.members.import` | Import-Assistent |
| `GET /backend/members/import/backup` | `backend.members.import.backup-download` | Backup herunterladen |
| `GET /backend/members/import/template` | `backend.members.import.template` | CSV-Vorlage herunterladen |