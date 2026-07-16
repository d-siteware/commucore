# Installation

Diese Anleitung beschreibt, wie du CommuCore auf einem lokalen Entwicklungssystem oder einem Produktionsserver installierst.

---

## Systemvoraussetzungen

CommuCore basiert auf dem Laravel-Framework. Stelle sicher, dass folgende Komponenten auf deinem System verfügbar sind:

| Komponente | Mindestversion | Hinweis |
|---|---|---|
| PHP | 8.3+ | Benötigte Extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath` |
| Composer | 2.x | PHP-Paketmanager |
| Node.js | 18+ | Für das Build-System |
| NPM oder PNPM | – | Zur Verwaltung von Frontend-Abhängigkeiten |
| MySQL / MariaDB oder PostgreSQL | – | Als Datenbank-Backend |
| Webserver | – | Nginx (empfohlen) oder Apache |
| Git | – | Zum Klonen des Repositories |

> **Tipp:** Auf lokalen Entwicklungssystemen eignet sich [Laravel Herd](https://herd.laravel.com/) oder [Laragon](https://laragon.org/) als einfache All-in-One-Umgebung.

---

## Repository klonen

Klone das CommuCore-Repository und wechsle in das Projektverzeichnis:

```bash
git clone https://github.com/commu-core/commucore.git
cd commucore
```

---

## PHP-Abhängigkeiten installieren

Installiere alle PHP-Pakete über Composer:

```bash
composer install
```

Für Produktionsumgebungen empfiehlt sich der optimierte Install-Befehl:

```bash
composer install --no-dev --optimize-autoloader
```

---

## Frontend-Abhängigkeiten installieren

Installiere die JavaScript-Abhängigkeiten:

```bash
npm install
```

Alternativ mit PNPM (schneller, empfohlen):

```bash
pnpm install
```

---

## Umgebung konfigurieren

Kopiere die mitgelieferte Beispiel-Umgebungsdatei:

```bash
cp .env.example .env
```

Generiere anschließend den Applikationsschlüssel:

```bash
php artisan key:generate
```

Öffne die `.env`-Datei und passe mindestens folgende Werte an:

```env
APP_NAME=CommuCore
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=commucore
DB_USERNAME=root
DB_PASSWORD=
```

> **Hinweis:** Stelle sicher, dass die Datenbank bereits existiert, bevor du die Migrationen ausführst.

---

## Datenbank einrichten

Führe die Datenbankmigrationen aus, um alle Tabellen anzulegen:

```bash
php artisan migrate
```

Falls du die Datenbank mit Beispiel- oder Standarddaten befüllen möchtest:

```bash
php artisan db:seed
```

---

## Einrichtungs-Wizard

Nach den Migrationen kannst du den interaktiven Installations-Wizard starten, der dich durch die Grundkonfiguration führt:

```bash
php artisan commucore:install
```

Der Wizard übernimmt automatisch die Einrichtung des Administrator-Kontos sowie der Organisationsinformationen. Weitere Details findest du auf der Seite [Installations-Wizard](./install-wizard).

---

## Verzeichnisberechtigungen & Storage

Die folgenden Verzeichnisse müssen vom Webserver beschreibbar sein:

```
storage/
bootstrap/cache/
```

Setze die Berechtigungen entsprechend (Linux/macOS):

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Erstelle anschließend den symbolischen Link für den öffentlichen Dateizugriff:

```bash
php artisan storage:link
```

---

## Assets bauen

### Entwicklung

Im Entwicklungsmodus mit Hot-Reload:

```bash
npm run dev
```

### Produktion

Für den Produktionseinsatz werden die Assets optimiert und minifiziert:

```bash
npm run build
```

---

## Anwendung starten

### Lokal (Entwicklung)

Starte den eingebauten Laravel-Entwicklungsserver:

```bash
php artisan serve
```

Die Anwendung ist anschließend unter folgender Adresse erreichbar:

```
http://localhost:8000
```

### Produktion

Für den Produktionsbetrieb wird ein vollwertiger Webserver empfohlen. Siehe dazu den Abschnitt [Produktionshinweise](#produktionshinweise).

---

## Produktionshinweise

### Empfohlener Stack

- **Nginx** als Webserver
- **PHP-FPM** für die PHP-Verarbeitung
- **Supervisor** zur Verwaltung von Queue-Workern
- **Cron** für den Laravel-Scheduler

### Nginx-Beispielkonfiguration

```nginx
server {
    listen 80;
    server_name deine-domain.de;
    root /var/www/commucore/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Laravel Scheduler (Cron)

Füge folgenden Eintrag zur Crontab des Webserver-Nutzers hinzu (`crontab -e`):

```bash
* * * * * cd /pfad/zu/commucore && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker (Supervisor)

Beispielkonfiguration für Supervisor (`/etc/supervisor/conf.d/commucore.conf`):

```ini
[program:commucore-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /pfad/zu/commucore/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/commucore-worker.log
```

### Worker-Neustart im Deployment

`sudo supervisorctl restart commucore-worker` kann **alle** Worker-Prozesse gleichzeitig stoppen, ohne dass ein neuer startet, wenn die Start-Phase durch das Deploy-Skript unterbrochen wird. Stattdessen immer:

```bash
sudo supervisorctl stop commucore-worker:*
sudo supervisorctl start commucore-worker:*
sudo supervisorctl status commucore-worker:*
```

Die dritte Zeile prüft, ob alle Prozesse wirklich `RUNNING` sind.

### Produktionsoptimierungen

Nach dem Deployment empfehlen sich folgende Befehle zur Performance-Optimierung:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Support

Für professionelle Installation, Hosting oder individuelle Anpassungen:

[https://commu-core.com](https://commu-core.com)