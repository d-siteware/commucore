# AGENTS.md — commucore (Instanz-Codebase)

Vereinsverwaltungs-App (Mitglieder, Buchhaltung, Events, Mails, SEPA). Läuft pro Kunde als eigene Instanz: geteilter Code unter `/var/source`, pro Instanz eigene SQLite + Storage unter `/var/instances/{subdomain}/` (`INSTANCE_PATH` steuert den Kontext).

## Harte Regeln

1. **Keine Queue-Annahmen.** Instanzen haben **keinen Queue-Worker** — alles läuft synchron:
   - Mails: `Mail::send(...)`, niemals `Mail::queue(...)`
   - Jobs: `dispatchSync(...)`, niemals `dispatch()`
   - Kein `implements ShouldQueue`, kein `->onQueue()`, kein `->delay()`
   - Instanz-Redis-Queues (`{subdomain}_queues:*`) werden nie konsumiert.
2. **Niemals auf dem Server editieren oder committen** (`/var/source`). Weg ist immer: lokal → Tests → Push → Deploy per Dashboard-Button in app.commucore.
3. **Null-Safety ist Pflicht** bei Relationen — `user->member`, `event->venue`, `transaction->account`, `member_transaction->member` u.a. können null sein. Guards symmetrisch halten (Asymmetrie = Bug).
4. **Audit/History** schreibt synchron (`HasHistory`-Trait → `RecordHistory::dispatchSync`). History-Fehler dürfen die Hauptoperation nie brechen.
5. **DSGVO**: Consent-Zeitstempel (`gdpr_consent_at` u.a.) werden nie fabriziert — nur setzen, wenn tatsächlich erteilt. Import-Backups (Voll-PII) haben 24h-Retention (`commucore:prune-import-backups`).
6. **Mails auf Trial/Demo** gehen ins Log (`MAIL_MAILER=log`, bewusst — Spam-Schutz). SMTP-Credentials werden erst bei aktiver Subscription in der Instanz-.env hinterlegt.

## Befehle

```bash
./vendor/bin/pest          # Tests
./vendor/bin/phpstan analyse
./vendor/bin/pint          # oder --test
```

## Deploy

Läuft **nie** von diesem Repo aus direkt auf dem Server per Hand, sondern über die Zentrale (app.commucore): Dashboard-Button → `commucore:deploy-update` (Wartungsmodus für alle Instanzen, git pull auf `/var/source`, composer/npm, Instanz-Migrationen, Worker-Restart).
