<?php

namespace ganzobenlinks\karneval;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use ganzobenlinks\karneval\Exception\KarnevalException;
use ganzobenlinks\karneval\Utils\Datefunctions;

/**
 * Calculates and displays/returns the dates for the Cologne Karneval for a given yeaer
 *
 * @example
 * $kareval = new Karneval(2026);
 * echo $karneval->asString();
 */
class Karneval
{
    private const int OFFSET_FROM_EASTER = -52;
    public readonly int $year;
    private string $lang = 'koelsch';
    private DateTimeZone $tz;

    /** @var array<int, array{id: string, name_de: string, name_koelsch: string, description: string, date: DateTimeImmutable|null}> */
    private array $stages = [];

    /**
     * Creates a new instance for the given year and timezone.
     *
     * Initializes internal configuration and performs the initial
     * date calculations for that year.
     *
     * @param int $year The target year for which data is calculated.
     * @param DateTimeZone|null $tz Optional timezone; defaults to 'Europe/Berlin' if not provided.
     */
    public function __construct(int $year, ?DateTimeZone $tz = null)
    {
        $this->year = $year;
        $this->tz = $tz ?? new DateTimeZone('Europe/Berlin');
        $this->config();
        $this->calculate();
    }

    private function calculate(): void
    {
        $easter = Datefunctions::getEasterSunday($this->year, $this->tz);
        // Offsets are fixed relative to Easter; they do not depend on leap years.
        $offset = self::OFFSET_FROM_EASTER;

        foreach ($this->stages as $i => $stage) {
            $this->stages[$i]['date'] = $easter->sub(new DateInterval('P' . abs($offset) . 'D'));
            $offset++;
        }
    }
    /**
     * Returns the date range of Karneval for the configured year.
     *
     * The date is formatted as a string in the format e.g. 12.02.2026 - 18.02.2026.
     *
     * @return string Formatted date of Rosenmontag for this instance’s year.
     */
    public function asString(): string
    {
        return $this->stages[0]['date']->format('d.m.Y') . ' - ' . $this->stages[6]['date']->format('d.m.Y');
    }
    /**
     * Returns the date of Rosenmontag for the configured year.
     *
     * The date is formatted as a string in the format 'd.m.Y'
     * (e.g. '03.03.2025').
     *
     * @return string Formatted date of Rosenmontag for this instance’s year.
     */
    public function rosenmontag(): string
    {
        return $this->stages[4]['date']->format('d.m.Y');

    }

    /**
     * Returns the object’s data as an associative array.
     *
     * The array structure is suitable for serialization or
     * conversion to JSON via {@see toJson()}.
     *
     * @return array<string, mixed> Object data as an associative array.
     */
    public function toArray(): array
    {
        $langKey = 'name_' . $this->lang;
        $result = [
            'year' => $this->year,
            'karneval' => [],
        ];
        foreach ($this->stages as $stage) {
            $result['karneval'][$stage['id']] = [
                'name' => $stage[$langKey],
                'description' => $stage['description'],
                'date' => $stage['date']?->format('d.m.Y'),
            ];
        }
        return $result;
    }

    /**
     * Returns the object’s data as a JSON-encoded string.
     *
     * Uses {@see toArray()} internally and throws a JsonException
     * if encoding fails.
     *
     * @return string JSON representation of the object.
     * @throws JsonException If encoding to JSON fails.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function setLang(string $lang): static
    {
        if (!in_array($lang, ['koelsch', 'de'], true)) {
            throw new KarnevalException('Invalid language, only "koelsch" and "de" are supported.');
        }
        $this->lang = $lang;
        return $this;
    }

    private function config(): void
    {
        $this->stages = [
            [
                'id' => 'weiberfastnacht',
                'name_de' => 'Weiberfastnacht',
                'name_koelsch' => 'Wieverfastelovend',
                'description' => 'Auftakt des Straßenkarnevals; Rathaussturm durch die „Wiever“.',
                'date' => null,
            ],
            [
                'id' => 'freitag',
                'name_de' => 'Karnevalsfreitag',
                'name_koelsch' => 'Freijdaach',
                'description' => 'Feiern in Kneipen und Sitzungen; kein offizieller Feiertag.',
                'date' => null,
            ],
            [
                'id' => 'samstag',
                'name_de' => 'Karnevalssamstag',
                'name_koelsch' => 'Samstaach',
                'description' => 'Veedelsumzüge und Kneipenkarneval.',
                'date' => null,
            ],
            [
                'id' => 'sonntag',
                'name_de' => 'Karnevalssonntag',
                'name_koelsch' => 'Sundaach',
                'description' => 'Viele Veedelszöch, z. B. „Schull- un Veedelszöch“ in Köln.',
                'date' => null,
            ],
            [
                'id' => 'rosenmontag',
                'name_de' => 'Rosenmontag',
                'name_koelsch' => 'Rusemondaach',
                'description' => 'Großer Rosenmontagszug – Höhepunkt des Straßenkarnevals.',
                'date' => null,
            ],
            [
                'id' => 'veilchendienstag',
                'name_de' => 'Veilchendienstag',
                'name_koelsch' => 'Veilchendienstdaach',
                'description' => 'Letzter Karnevalstag; abends oft Nubbelverbrennung.',
                'date' => null,
            ],
            [
                'id' => 'aschermittwoch',
                'name_de' => 'Aschermittwoch',
                'name_koelsch' => 'Äschermettwoch',
                'description' => 'Fastelovend es vorbei – Fastenzeit beginnt.',
                'date' => null,
            ],
        ];
    }

}