# Installations-Wizard

CommuCore bringt einen interaktiven Kommandozeilen-Wizard mit, der die initiale Einrichtung deiner Instanz Schritt für Schritt begleitet. Er ersetzt das manuelle Ausführen einzelner Artisan-Befehle und führt dich durch Datenbankmigrationen, Administrator-Erstellung und Organisationskonfiguration.

---

## Wizard starten

```bash
php artisan commucore:install
```

Nach dem Start begrüßt dich der Wizard mit einem Einleitungsbildschirm und führt dich durch die einzelnen Schritte.

---

## Schritte im Überblick

### 1. Datenbankmigrationen

Der Wizard fragt zunächst, ob die Migrationen ausgeführt werden sollen:

```
Run database migrations? (yes/no) [yes]
```

Bei Bestätigung werden alle Migrationen automatisch mit `--force` ausgeführt – das macht den Wizard auch für Produktionsumgebungen geeignet, ohne dass ein separates `php artisan migrate` notwendig ist.

---

### 2. Administrator-Konto

Im zweiten Schritt wird ein Administrator-Konto angelegt. Der Wizard fragt folgende Angaben ab:

| Feld | Beschreibung |
|---|---|
| Name | Anzeigename des Administrators |
| E-Mail | Login-E-Mail (muss eindeutig sein) |
| Passwort | Mindestens 8 Zeichen, wird zur Bestätigung wiederholt |

Alle Eingaben werden direkt validiert. Ungültige Werte (z.B. bereits verwendete E-Mail-Adresse, zu kurzes Passwort) werden sofort zurückgewiesen und der Wizard fragt erneut.

> **Hinweis:** Existiert bereits ein Administrator-Konto, weist der Wizard darauf hin. Du kannst dann entscheiden, ob ein weiterer Admin angelegt werden soll.

---

### 3. Organisationsinformationen

Im dritten Schritt werden grundlegende Informationen zur Organisation erfasst:

**Basisdaten:**

| Feld | Beschreibung |
|---|---|
| Name | Name der Organisation (Standard: `APP_NAME` aus `.env`) |
| E-Mail | Kontakt-E-Mail der Organisation |
| Website | Öffentliche Website |

**Mehrsprachiger Slogan & Beschreibung:**

CommuCore unterstützt mehrsprachige Inhalte. Der Wizard fragt Slogan und Beschreibung für alle konfigurierten Sprachen ab (Standard: Deutsch und Englisch). Die verfügbaren Sprachen werden über `config('app.available_locales')` gesteuert.

**Rechtliche Angaben** *(optional)*:

| Feld | Beschreibung |
|---|---|
| Registernummer | Handelsregisternummer o.ä. |
| Eintragungsdatum | Datum im Format `YYYY-MM-DD` |
| Zuständiges Gericht | z.B. Amtsgericht München |
| Steuer-ID | Steueridentifikationsnummer |
| USt-IdNr. | Umsatzsteuer-Identifikationsnummer |

Alle rechtlichen Felder sind optional und können übersprungen werden.

---

## Optionen & Flags

Der Wizard unterstützt zwei Flags, um einzelne Schritte zu überspringen:

```bash
# Administrator-Erstellung überspringen
php artisan commucore:install --skip-admin

# Organisationseinrichtung überspringen
php artisan commucore:install --skip-organization

# Beides überspringen (nur Migrationen)
php artisan commucore:install --skip-admin --skip-organization
```

Diese Flags sind nützlich, wenn z.B. bei einem Re-Deployment nur die Migrationen erneut ausgeführt werden sollen, ohne bestehende Daten zu überschreiben.

---

## Nachträgliche Anpassungen

Alle im Wizard eingetragenen Organisationseinstellungen können später im Admin-Panel unter **Einstellungen → Branding** angepasst werden. Ein erneutes Ausführen des Wizards ist dafür nicht notwendig.

---

## Beispiel-Durchlauf

```
╔════════════════════════════════════════════════════════════╗
║                                                            ║
║              CommuCore Installation Wizard                 ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝

This wizard will guide you through the initial setup of your
CommuCore instance.

Run database migrations? (yes/no) [yes]: yes
Running migrations...
✓ Migrations completed

═══════════════════════════════════════════════════════════
  Administrator Account Setup
═══════════════════════════════════════════════════════════

Administrator name: Max Mustermann
Administrator email: admin@beispiel.de
Administrator password:
Confirm password:
✓ Administrator account created successfully

═══════════════════════════════════════════════════════════
  Organization Information Setup
═══════════════════════════════════════════════════════════

Basic Information:

Organization name [CommuCore]: Mein Verein e.V.
Organization email: kontakt@mein-verein.de
Organization website: https://mein-verein.de

Organization Slogan (multilingual):

Slogan (German): Gemeinsam mehr erreichen
Slogan (English): Achieving more together

...

✓ Organization information saved successfully

╔════════════════════════════════════════════════════════════╗
║                                                            ║
║              Installation Completed Successfully!          ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝

Your CommuCore instance is now ready to use.
You can further customize your organization settings in the
admin panel under Settings > Branding.
```