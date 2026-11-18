<?php

namespace Tests\Unit;

use App\Rules\Checksums;
use App\Rules\CountryFormat;
use App\Rules\NationalId;
use App\Support\Country;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidatorsTest extends TestCase
{
    public function test_spanish_dni_and_nie(): void
    {
        $this->assertTrue(Checksums::spanishDniNie('12345678Z'));
        $this->assertTrue(Checksums::spanishDniNie('X1234567L'));
        $this->assertTrue(Checksums::spanishDniNie('Y1234567X'));
        $this->assertFalse(Checksums::spanishDniNie('12345678A'), 'wrong control letter');
        $this->assertFalse(Checksums::spanishDniNie('X1234567A'));
        $this->assertFalse(Checksums::spanishDniNie('1234567Z'), 'too short');
        $this->assertTrue(NationalId::check('es_dni_nie', NationalId::normalize(' x-1234567-l ')));
    }

    public static function ibans(): array
    {
        return [
            'ES' => ['ES9121000418450200051332', 'ES', 24],
            'GB' => ['GB82WEST12345698765432', 'GB', 22],
            'DE' => ['DE89370400440532013000', 'DE', 22],
            'IT' => ['IT60X0542811101000000123456', 'IT', 27],
            'NL' => ['NL91ABNA0417164300', 'NL', 18],
            'GR' => ['GR1601101250000000012300695', 'GR', 27],
            'PT' => ['PT50000201231234567890154', 'PT', 25],
            'SK' => ['SK3112000000198742637541', 'SK', 24],
            'SI' => ['SI56263300012039086', 'SI', 19],
        ];
    }

    #[DataProvider('ibans')]
    public function test_iban_mod97_and_country_length(string $iban, string $cc, int $len): void
    {
        $this->assertTrue(Checksums::iban($iban, $cc, $len));
        // One digit changed => mod-97 fails.
        $broken = substr($iban, 0, -1).((int) substr($iban, -1) + 1) % 10;
        $this->assertFalse(Checksums::iban($broken, $cc, $len));
        // Right checksum, wrong country for the form.
        $this->assertFalse(Checksums::iban($iban, $cc === 'ES' ? 'DE' : 'ES', $len));
        // Wrong length for the country.
        $this->assertFalse(Checksums::iban($iban, $cc, $len + 1));
    }

    public function test_portuguese_nif(): void
    {
        $this->assertTrue(Checksums::portugueseNif('123456789'));
        $this->assertTrue(Checksums::portugueseNif('501964843'));
        $this->assertFalse(Checksums::portugueseNif('123456780'));
        $this->assertFalse(Checksums::portugueseNif('12345678'));
    }

    public function test_greek_afm(): void
    {
        $this->assertTrue(Checksums::greekAfm('094014201'));
        $this->assertTrue(Checksums::greekAfm('123456783'));
        $this->assertFalse(Checksums::greekAfm('094014202'));
        $this->assertFalse(Checksums::greekAfm('000000000'));
    }

    public function test_italian_codice_fiscale(): void
    {
        $this->assertTrue(Checksums::italianCodiceFiscale('RSSMRA85T10A562S'));
        $this->assertFalse(Checksums::italianCodiceFiscale('RSSMRA85T10A562T'));
        $this->assertFalse(Checksums::italianCodiceFiscale('RSSMRA85T10A562'));
    }

    public function test_slovak_birth_number(): void
    {
        $this->assertTrue(Checksums::slovakBirthNumber('900101/1239'));
        $this->assertTrue(Checksums::slovakBirthNumber('9051011233'), 'women: month + 50');
        $this->assertTrue(Checksums::slovakBirthNumber('530101/123'), '9 digits before 1954');
        $this->assertFalse(Checksums::slovakBirthNumber('900101/1236'), 'not divisible by 11');
        $this->assertFalse(Checksums::slovakBirthNumber('901301/1234'), 'invalid month');
        $this->assertFalse(Checksums::slovakBirthNumber('600101/123'), '9 digits after 1953');
    }

    public function test_slovene_emso(): void
    {
        $this->assertTrue(Checksums::sloveneEmso('0101990500003'));
        $this->assertFalse(Checksums::sloveneEmso('0101990500006'));
        $this->assertFalse(Checksums::sloveneEmso('3213990500003'), 'invalid month');
    }

    public function test_uk_nino_and_malagasy_ids(): void
    {
        $this->assertTrue(Checksums::ukNino('AB123456C'));
        $this->assertFalse(Checksums::ukNino('GB123456A'), 'forbidden prefix');
        $this->assertTrue(Checksums::malagasyCin('101011234567'));
        $this->assertFalse(Checksums::malagasyCin('10101123456'));
        $this->assertTrue(Checksums::malagasyRib('00005000011234567890159'));
        $this->assertFalse(Checksums::malagasyRib('00005000011234567890158'));
    }

    public static function postalCodes(): array
    {
        return [
            ['es', '28001', true], ['es', '53001', false], ['es', '00100', false],
            ['gb', 'SW1A 1AA', true], ['gb', 'm1 1ae', true], ['gb', 'EC1A1BB', true], ['gb', '12345', false],
            ['de', '10115', true], ['de', '1011', false],
            ['it', '00184', true], ['it', '0018', false],
            ['nl', '1012 AB', true], ['nl', '1012ab', true], ['nl', '1012', false],
            ['gr', '105 57', true], ['gr', '10557', true], ['gr', '1055', false],
            ['pt', '1100-148', true], ['pt', '1100148', false],
            ['sk', '811 01', true], ['sk', '81101', true], ['sk', '8110', false],
            ['si', '1000', true], ['si', '10000', false],
            ['mg', '101', true], ['mg', '1010', false],
        ];
    }

    #[DataProvider('postalCodes')]
    public function test_postal_codes(string $cc, string $value, bool $valid): void
    {
        $this->assertSame($valid, CountryFormat::matches(new Country($cc), 'postal', $value), "$cc $value");
    }

    public static function phones(): array
    {
        return [
            ['es', '612 345 678', true], ['es', '+34 712345678', true], ['es', '912345678', false],
            ['gb', '07400 123456', true], ['gb', '+44 7400 123456', true], ['gb', '0207 123 4567', false],
            ['de', '0151 23456789', true], ['de', '030 1234567', false],
            ['it', '312 345 6789', true], ['it', '06 1234567', false],
            ['nl', '06 12345678', true], ['nl', '020 1234567', false],
            ['gr', '691 234 5678', true], ['gr', '210 1234567', false],
            ['pt', '912 345 678', true], ['pt', '942 345 678', false],
            ['sk', '0912 345 678', true], ['sk', '02 1234 5678', false],
            ['si', '041 234 567', true], ['si', '01 234 5678', false],
            ['mg', '034 12 345 67', true], ['mg', '+261 32 12 345 67', true], ['mg', '020 22 123 45', false],
        ];
    }

    #[DataProvider('phones')]
    public function test_mobile_phones(string $cc, string $value, bool $valid): void
    {
        $this->assertSame($valid, CountryFormat::matches(new Country($cc), 'phone', $value), "$cc $value");
    }

    public function test_number_plates(): void
    {
        $this->assertTrue(CountryFormat::matches(new Country('es'), 'plate', '1234 bcd'));
        $this->assertFalse(CountryFormat::matches(new Country('es'), 'plate', '1234 ABC'), 'vowels are not used');
        $this->assertTrue(CountryFormat::matches(new Country('gb'), 'plate', 'AB12 CDE'));
        $this->assertTrue(CountryFormat::matches(new Country('it'), 'plate', 'AB 123 CD'));
        $this->assertTrue(CountryFormat::matches(new Country('nl'), 'plate', 'AB-123-C'));
        $this->assertTrue(CountryFormat::matches(new Country('mg'), 'plate', '1234 TBA'));
    }
}
