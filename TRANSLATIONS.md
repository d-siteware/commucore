# 🌍 Translations Guide

CommuCore is fully multilingual and relies on **PHP array-based translation files**.

Translations are a core part of the project — contributors are very welcome to help improve and extend language support.

---

## 📁 Translation Structure

All translations are located in:

```
lang/{locale}/app.php
```

Example:

```
lang/
├── de/
│   └── app.php
├── en/
│   └── app.php
└── hu/
    └── app.php
```

Each file returns a simple PHP array:

```php
<?php

return [
    'welcome' => 'Welcome',
];
```

---

## 🧩 Translation Keys

### Flat keys

```php
'welcome' => 'Willkommen',
'home' => 'Startseite',
'privacy' => 'Datenschutz',
```

Usage in Blade / Livewire:

```php
__('app.welcome')
```

---

### Nested keys

Translations can also be grouped logically:

```php
'login' => [
    'title' => 'Mitglieder-Login',
    'header' => 'Melden Sie sich an',
],
```

Usage:

```php
__('app.login.title')
```

---

## 🗂️ Naming Conventions

Please follow these rules when adding or editing translations:

### ✅ Use dot notation
```php
'login.title'
'password.reset.title'
```

or nested arrays:

```php
'password' => [
    'reset' => [
        'title' => '...',
    ],
],
```

Both approaches are valid — consistency within a section is important.

---

### ✅ Use semantic keys (not sentences)

❌ Bad:
```php
'please_click_here_to_login' => 'Login now'
```

✅ Good:
```php
'login.btn.login.label' => 'Login now'
```

Keys describe **meaning**, not text length.

---

### ✅ Keep keys language-neutral

❌ Bad:
```php
'login_de' => 'Anmelden'
```

✅ Good:
```php
'login.btn.login.label' => "Anmelden"
```

---

## 🌐 Supported Locales

Currently supported languages:

- `de` — German
- `hu` — Hungarian
- `en` — English (in progress)

New languages are welcome!

Locale codes must follow ISO 639-1:

- `fr` → French
- `it` → Italian
- `es` → Spanish
- `pl` → Polish

---

## ➕ Adding a New Language

1. Create a new directory:

```
lang/fr/
```

2. Copy an existing translation file:

```
cp lang/de/app.php lang/fr/app.php
```

3. Translate all values (do **not** change keys).

4. Submit a pull request.

---

## ⚠️ Important Rules

### ❗ Never change translation keys
Changing keys may break:

- Blade views
- Livewire components
- Validation messages

If a key must be changed, open an issue first.

---

### ❗ Do not translate keys

Only translate values:

```php
// ❌ wrong
'bienvenue' => 'Willkommen'

// ✅ correct
'welcome' => 'Bienvenue'
```

---

## 🧪 Validation & Testing

Before submitting a translation:

- Check PHP syntax
- Make sure commas are correct
- Ensure UTF-8 encoding
- Test language switching if possible

Laravel will throw an error if translation files are invalid PHP.

---

## 📐 Calendar Translations

Some translations contain structured data, e.g.:

```php
'cal' => [
    'day_short' => [
        'Mo' => 'M',
    ],
],
```

Please preserve:

- Array structure
- Original keys
- Ordering

Only translate the values.

---

## 🤝 Contribution Workflow

1. Fork the repository
2. Create a branch:
   ```
   feature/translation-fr
   ```
3. Commit changes
4. Open a Pull Request

Please mention in the PR description:

- Language
- Completeness (full / partial)
- Native or non-native translation

---

## 💬 Need Help?

If you're unsure about wording, grammar or context:

- Open a discussion
- Ask in the issue
- Submit partial translations — that's perfectly fine

---

## ❤️ Thank You

Translations make CommuCore accessible to communities worldwide.

Every contribution helps — even small improvements matter.

---

> **CommuCore**  
> building sustainable communities through open software
