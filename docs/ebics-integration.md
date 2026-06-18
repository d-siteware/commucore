# EBICS-Integration in CommuCore

## Überblick

EBICS (Electronic Banking Internet Communication Standard) ist das von deutschen
Banken verwendete Protokoll für den sicheren Dateiaustausch. CommuCore nutzt
EBICS, um SEPA-Lastschriftdateien (pain.008) automatisiert an die Hausbank zu
übermitteln – ohne manuelles Hochladen ins Online-Banking.

## Voraussetzungen

- EBICS-Vertrag mit der Hausbank (unterstützt EBICS 2.4 oder 2.5)
- Zugangsdaten von der Bank:
  - **Host-URL**: z. B. `https://ebics.bank.de/ebics/ebics.aspx`
  - **Host-ID**: Kürzel der Bank, z. B. `BANKDEFFXXX`
  - **Partner-ID**: vom Institut vergeben, z. B. `12345`
  - **User-ID**: vom Institut vergeben, z. B. `U12345`
- PHP 8.3+ mit Extensions: `bcmath`, `curl`, `dom`, `json`, `libxml`, `openssl`, `zip`, `zlib`

## Der EBICS-Handshake (einmalige Einrichtung)

Bevor Dateien übertragen werden können, muss ein Vertrauensverhältnis zwischen
CommuCore und der Bank aufgebaut werden. Der Ablauf gliedert sich in vier
Phasen:

### Phase 1: Schlüssel erzeugen

CommuCore generiert drei asymmetrische RSA-Schlüsselpaare (A, E, X):

| Schlüssel | Zweck | Verwendung |
|-----------|-------|------------|
| **A (Signatur)** | Elektronische Signatur | Wird via INI-Order an die Bank gesendet |
| **X (Authentifizierung)** | Authentisierung | Wird via HIA-Order an die Bank gesendet |
| **E (Verschlüsselung)** | Datenverschlüsselung | Wird via HIA-Order an die Bank gesendet |

Die Schlüssel werden AES-verschlüsselt in `storage/app/sepa/ebics/keyring.json`
gespeichert.

### Phase 2: Schlüssel an Bank senden (INI + HIA)

Das System sendet zwei Orders an die Bank:

1. **INI** – übermittelt den öffentlichen Signaturschlüssel A
2. **HIA** – übermittelt die öffentlichen Schlüssel X und E

### Phase 3: Bankbrief (INI/HIA Letter)

Nach dem elektronischen Versand erzeugt CommuCore einen **Bankbrief als PDF**.
Dieser enthält die Fingerprints (Hashwerte) der gesendeten Schlüssel. Der Brief
muss **ausgedruckt, unterschrieben und per Post an die Bank geschickt werden**.

Die Bank gleicht die unterschriebenen Hashwerte mit den elektronisch
empfangenen Schlüsseln ab und aktiviert den EBICS-Zugang.

### Phase 4: Bankschlüssel abrufen (HPB)

Sobald die Bank den Zugang aktiviert hat, führt CommuCore den **HPB-Order**
aus. Dabei werden die öffentlichen Schlüssel der Bank heruntergeladen und im
Keyring gespeichert. Diese werden für die Verschlüsselung und Signatur späterer
Dateiübertragungen benötigt.

**Erst nach diesem Schritt ist das System bereit für Datei-Uploads.**

## Regelmäßiger Betrieb: FUL-Upload

Nach erfolgreichem Handshake läuft der monatliche/jährliche Einzug
automatisiert ab:

1. CommuCore ermittelt alle offenen Beitragszahlungen mit aktivem SEPA-Mandat
2. `SepaDirectDebitService` generiert eine `pain.008.001.09` XML
3. `EbicsService::uploadXml()` wickelt den **FUL-Order** (File Upload) ab:
   - Erstellt `FULContext` mit Dateiformat und Ländercode `DE`
   - Verschlüsselt, signiert und überträgt die Datei an die Bank
   - Die Bank quittiert den Erhalt mit einem technischen Return-Code

## Architektur

### `EbicsService` (app/Services/Sepa/EbicsService.php)

| Methode | Beschreibung |
|---------|-------------|
| `initialize()` | Erzeugt User Signatures (A, E, X) |
| `sendIni()` | Sendet INI-Order (Signaturschlüssel A) |
| `sendHia()` | Sendet HIA-Order (Schlüssel X + E) |
| `downloadHpb()` | Holt Bankschlüssel via HPB |
| `uploadXml(xml, format)` | Lädt pain.008 via FUL hoch |
| `generateBankLetterPdf()` | Erzeugt Bankbrief-PDF |
| `isConfigured()` | Prüft, ob EBICS-Zugangsdaten hinterlegt sind |
| `isInitialized()` | Prüft, ob keyring.json existiert |
| `isReadyForUpload()` | Prüft, ob Bankschlüssel geladen sind |

### Artisan-Befehle (für Terminal-Zugriff)

```bash
# Komplette Ersteinrichtung (Schlüssel → INI → HIA → Bankbrief)
php artisan commucore:ebics-setup

# Bankschlüssel laden (nach Aktivierung durch die Bank)
php artisan commucore:ebics-setup --hpb-only

# Batch-XML für offene Beitragszahlungen generieren
php artisan commucore:collect-sepa-fees --year=2026
php artisan commucore:collect-sepa-fees --year=2026 --dry-run  # nur Vorschau
```

### UI-Zugang (für Administratoren ohne Terminal)

Alle EBICS-Aktionen sind auch über die Weboberfläche ausführbar:

1. **Einstellungen → SEPA-Tab** (`/backend/settings`):
   - EBICS-Zugangsdaten hinterlegen
   - "EBICS initialisieren" → Schlüssel + INI + HIA + Bankbrief
   - "Bankbrief herunterladen" → PDF mit den Schlüssel-Fingerprints
   - "Bankschlüssel abrufen (HPB)" → nach Bank-Aktivierung
   - "Verbindung prüfen" → Statusanzeige
   - Ein Log-Fenster zeigt detaillierte Rückmeldungen

2. **Buchhaltung → SEPA-Sammelübersicht** (`/backend/sepa-collections`):
   - Übersicht ausstehender Lastschriften
   - "XML generieren" → Batch-XML erstellen und herunterladen
   - Tab "Rücklastschriften": fehlgeschlagene Einzüge verwalten, Wiedereinzug

## Rücklastschriften

Wenn eine Lastschrift von der Bank zurückgegeben wird (z. B. wegen
Kontodeckung), kann der Vorgang in der SEPA-Sammelübersicht bearbeitet werden:

1. Die Transaktion wird auf "zurückgegeben" gesetzt
2. Das Mitglied erhält eine E-Mail-Benachrichtigung
3. Bei aktivem Mandat kann ein **Wiedereinzug** gestartet werden
4. Es wird eine neue "eingereichte" Transaktion erstellt

## Logging

Während der Anfangsphase protokolliert CommuCore alle EBICS-Operationen in
`storage/logs/laravel.log` (Log-Level: `info`). So können Fehler auch ohne
Terminal-Zugriff vom Administrator nachvollzogen werden.

## Technische Details

- **Library**: `ebics-api/ebics-client-php` (v3.0.3)
- **EBICS-Version**: 2.5 (FUL wird nur von 2.4/2.5 unterstützt)
- **Schlüsselspeicher**: `storage/app/sepa/ebics/keyring.json` (AES-verschlüsselt)
- **Bankbrief-Format**: PDF (via FPDF, in der Library enthalten)
- **Dateiformat für FUL**: `pain.008.001.02` (von deutschen Banken gefordert)
