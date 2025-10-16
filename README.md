### ganzobenlinks/karneval

A tiny PHP library to calculate the Cologne (Kölsche) Karneval dates for a given year, with simple helpers to format or serialize the results.

- Namespace: `ganzobenlinks\karneval`
- PHP: `>= 8.0`
- License: MIT

---

### Features
- Compute the seven key Karneval days for any year
- Output as string range, array, or JSON
- Localized names (Kölsch and German)
- Timezone-aware (defaults to `Europe/Berlin`)

---

### Installation
Install via Composer:

```bash
composer require ganzobenlinks/karneval
```

Make sure your project loads Composer’s autoloader:

```php
<?php
require __DIR__ . '/vendor/autoload.php';
```

---

### Requirements
- PHP `>= 8.0`
- PHP extension `ext-calendar` (used for Easter computation)

---

### Quick start

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use ganzobenlinks\karneval\Karneval;

$karneval = new Karneval(2026); // defaults to Europe/Berlin timezone and Kölsch labels

echo $karneval->asString();     // e.g. "12.02.2026 - 18.02.2026"
```

---

### Usage examples

#### Choose language (Kölsch or German)
```php
use ganzobenlinks\karneval\Karneval;

$karneval = (new Karneval(2026))
    ->setLang('de');            // 'koelsch' (default) or 'de'

print_r($karneval->toArray());
```

Example structure of `toArray()` (dates depend on the year):
```json
{
  "year": 2026,
  "karneval": {
    "weiberfastnacht": {
      "name": "Weiberfastnacht",
      "description": "Auftakt des Straßenkarnevals; Rathaussturm durch die „Wiever“.",
      "date": "dd.mm.yyyy"
    },
    "freitag": {
      "name": "Karnevalsfreitag",
      "description": "Feiern in Kneipen und Sitzungen; kein offizieller Feiertag.",
      "date": "dd.mm.yyyy"
    },
    "samstag": { "name": "Karnevalssamstag", "description": "Veedelsumzüge und Kneipenkarneval.", "date": "dd.mm.yyyy" },
    "sonntag": { "name": "Karnevalssonntag", "description": "Viele Veedelszöch, z. B. „Schull- un Veedelszöch“ in Köln.", "date": "dd.mm.yyyy" },
    "rosenmontag": { "name": "Rosenmontag", "description": "Großer Rosenmontagszug – Höhepunkt des Straßenkarnevals.", "date": "dd.mm.yyyy" },
    "veilchendienstag": { "name": "Veilchendienstag", "description": "Letzter Karnevalstag; abends oft Nubbelverbrennung.", "date": "dd.mm.yyyy" },
    "aschermittwoch": { "name": "Aschermittwoch", "description": "Fastelovend es vorbei – Fastenzeit beginnt.", "date": "dd.mm.yyyy" }
  }
}
```

#### Get Rosenmontag only
```php
use ganzobenlinks\karneval\Karneval;

$k = new Karneval(2025);
echo $k->rosenmontag(); // prints 'dd.mm.yyyy'
```

#### Serialize to JSON
```php
use ganzobenlinks\karneval\Karneval;

$k = (new Karneval(2027))->setLang('koelsch');
$json = $k->toJson();
// {"year":2027,"karneval":{"weiberfastnacht":{...}, ...}}
```

#### Use a specific timezone
```php
use DateTimeZone;
use ganzobenlinks\karneval\Karneval;

$tz = new DateTimeZone('Europe/Berlin');
$k = new Karneval(2030, $tz);
```

---

### API

#### `new Karneval(int $year, ?DateTimeZone $tz = null)`
Create an instance for a given year and timezone.
- `year`: The year to calculate Karneval dates for
- `tz`: Optional `DateTimeZone` (default: `Europe/Berlin`)

#### `setLang(string $lang): static`
Set language for stage names. Allowed values: `koelsch` (default) or `de`.

#### `asString(): string`
Returns the full Karneval date range as `dd.mm.YY - dd.mm.YY`.

#### `rosenmontag(): string`
Returns only the Rosenmontag date as `dd.mm.YY`.

#### `toArray(): array`
Returns all data as an associative array with stable IDs for each stage.

#### `toJson(): string`
JSON-encoded version of `toArray()` using `JSON_THROW_ON_ERROR`.

---

### Stage IDs
Stable identifiers you can rely on for indexing:
- `weiberfastnacht`
- `freitag`
- `samstag`
- `sonntag`
- `rosenmontag`
- `veilchendienstag`
- `aschermittwoch`

---

### Error handling
Certain validation errors (e.g., invalid language) throw `ganzobenlinks\karneval\Exception\KarnevalException`.

---

### Example script
A tiny executable script you can drop into your project root:

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use ganzobenlinks\karneval\Karneval;

$year = (int)($argv[1] ?? date('Y'));
$lang = (string)($argv[2] ?? 'koelsch');

$k = (new Karneval($year))->setLang($lang);

echo "Karneval $year: " . $k->asString() . PHP_EOL;
echo "Rosenmontag:   " . $k->rosenmontag() . PHP_EOL;
```

Run it:
```bash
php karneval.php 2026 de
```

---

### Contributing
- Issues: `https://github.com/ganzobenlinks/karneval/issues`
- Pull Requests welcome! Please run `composer validate` and add tests if you introduce logic changes.

---

### License
MIT © Norman Fiedler
