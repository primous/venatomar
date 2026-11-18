<?php

namespace App\Support;

/**
 * Directory of supported countries and their official/premier banks.
 * Automatically feeds admin forms, client withdrawal bank selection, and IBAN/currency resolution.
 */
final class BankDirectory
{
    public const COUNTRIES = [
        'Uruguay' => [
            'code' => 'UY',
            'currency' => 'UYU',
            'banks' => [
                'Banco República (BROU)',
                'Banco Santander Uruguay',
                'Banco Itaú Uruguay',
                'BBVA Uruguay',
                'Scotiabank Uruguay',
                'HSBC Bank Uruguay',
                'Banco Heritage Uruguay',
                'Bandepe',
            ],
        ],
        'El Salvador' => [
            'code' => 'SV',
            'currency' => 'USD',
            'banks' => [
                'Banco Agrícola',
                'Banco Cuscatlán',
                'Banco Davivienda El Salvador',
                'BAC Credomatic El Salvador',
                'Banco Promerica El Salvador',
                'Banco Hipotecario de El Salvador',
                'Banco de Fomento Agropecuario',
                'Banco Azul de El Salvador',
                'Banco Industrial El Salvador',
            ],
        ],
        'Perú' => [
            'code' => 'PE',
            'currency' => 'PEN',
            'banks' => [
                'Banco de Crédito del Perú (BCP)',
                'BBVA Perú',
                'Interbank',
                'Scotiabank Perú',
                'Banco Pichincha Perú',
                'Banco de la Nación',
                'MiBanco',
                'BanBif',
                'Banco GNB Perú',
                'Banco Ripley',
                'Banco Falabella',
            ],
        ],
        'México' => [
            'code' => 'MX',
            'currency' => 'MXN',
            'banks' => [
                'BBVA México',
                'Banco Santander México',
                'Banorte',
                'Citibanamex',
                'HSBC México',
                'Scotiabank México',
                'Banco Azteca',
                'BanCoppel',
                'Inbursa',
                'Banco del Bajío (BanBajío)',
                'Afirme',
                'Bansefi / Banco del Bienestar',
            ],
        ],
        'España' => [
            'code' => 'ES',
            'currency' => 'EUR',
            'banks' => [
                'Banco Santander',
                'BBVA España',
                'CaixaBank',
                'Banco Sabadell',
                'Bankinter',
                'ING España',
                'Unicaja Banco',
                'Abanca',
                'Ibercaja',
                'Kutxabank',
                'Openbank',
            ],
        ],
        'France' => [
            'code' => 'FR',
            'currency' => 'EUR',
            'banks' => [
                'BNP Paribas',
                'Société Générale',
                'Crédit Agricole',
                'Banque Populaire',
                'Caisse d’Épargne',
                'LCL',
                'Crédit Mutuel',
                'La Banque Postale',
                'Boursorama Banque',
                'CIC',
                'Fortuneo',
            ],
        ],
        'Belgique' => [
            'code' => 'BE',
            'currency' => 'EUR',
            'banks' => [
                'BNP Paribas Fortis',
                'KBC Bank',
                'Belfius Banque',
                'ING Belgique',
                'Argenta',
                'Crelan',
            ],
        ],
        'Suisse' => [
            'code' => 'CH',
            'currency' => 'CHF',
            'banks' => [
                'UBS Switzerland',
                'Credit Suisse',
                'Banque Cantonale de Genève (BCGE)',
                'Banque Cantonale Vaudoise (BCV)',
                'Raiffeisen Suisse',
                'PostFinance',
                'Julius Bär',
            ],
        ],
        'Portugal' => [
            'code' => 'PT',
            'currency' => 'EUR',
            'banks' => [
                'Caixa Geral de Depósitos (CGD)',
                'Millennium BCP',
                'Novo Banco',
                'Banco Santander Totta',
                'Banco BPI',
                'Banco Montepio',
            ],
        ],
        'Italia' => [
            'code' => 'IT',
            'currency' => 'EUR',
            'banks' => [
                'Intesa Sanpaolo',
                'UniCredit',
                'Banco BPM',
                'BPER Banca',
                'Banca Monte dei Paschi di Siena',
                'Poste Italiane (BancoPosta)',
                'Credem',
            ],
        ],
        'Deutschland' => [
            'code' => 'DE',
            'currency' => 'EUR',
            'banks' => [
                'Deutsche Bank',
                'Commerzbank',
                'KfW Bankengruppe',
                'DZ Bank',
                'ING-DiBa Deutschland',
                'N26',
                'DKB (Deutsche Kreditbank)',
            ],
        ],
        'United Kingdom' => [
            'code' => 'GB',
            'currency' => 'GBP',
            'banks' => [
                'Barclays',
                'HSBC UK',
                'Lloyds Bank',
                'NatWest',
                'Standard Chartered',
                'Santander UK',
                'Revolut',
                'Monzo',
            ],
        ],
        'United States' => [
            'code' => 'US',
            'currency' => 'USD',
            'banks' => [
                'JPMorgan Chase',
                'Bank of America',
                'Wells Fargo',
                'Citibank',
                'U.S. Bancorp',
                'PNC Bank',
                'Truist Bank',
                'Capital One',
            ],
        ],
        'Canada' => [
            'code' => 'CA',
            'currency' => 'CAD',
            'banks' => [
                'Royal Bank of Canada (RBC)',
                'Toronto-Dominion Bank (TD)',
                'Scotiabank Canada',
                'Bank of Montreal (BMO)',
                'Canadian Imperial Bank of Commerce (CIBC)',
                'National Bank of Canada',
                'Desjardins',
            ],
        ],
        'Maroc' => [
            'code' => 'MA',
            'currency' => 'MAD',
            'banks' => [
                'Attijariwafa Bank',
                'Banque Populaire du Maroc (BCP)',
                'Bank of Africa (BMCE Group)',
                'Société Générale Maroc',
                'BMCI',
                'CIH Bank',
                'Crédit du Maroc',
            ],
        ],
        'Sénégal' => [
            'code' => 'SN',
            'currency' => 'XOF',
            'banks' => [
                'Société Générale Sénégal',
                'CBAO Groupe Attijariwafa bank',
                'Ecobank Sénégal',
                'Bank of Africa Sénégal',
                'BICI Sénégal',
                'Orabank Sénégal',
                'Coris Bank International',
            ],
        ],
        'Côte d’Ivoire' => [
            'code' => 'CI',
            'currency' => 'XOF',
            'banks' => [
                'Société Générale Côte d’Ivoire',
                'Ecobank Côte d’Ivoire',
                'BICICI',
                'Bank of Africa Côte d’Ivoire',
                'NSIA Banque',
                'SIB (Société Ivoirienne de Banque)',
                'Coris Bank Côte d’Ivoire',
            ],
        ],
        'Bénin' => [
            'code' => 'BJ',
            'currency' => 'XOF',
            'banks' => [
                'Bank of Africa Bénin',
                'Ecobank Bénin',
                'Société Générale Bénin',
                'BGFI Bank Bénin',
                'Orabank Bénin',
                'Coris Bank Bénin',
                'NSIA Banque Bénin',
            ],
        ],
        'Cameroun' => [
            'code' => 'CM',
            'currency' => 'XAF',
            'banks' => [
                'Afriland First Bank',
                'Société Générale Cameroun',
                'BICEC',
                'SCB Cameroun',
                'Ecobank Cameroun',
                'UBA Cameroun',
                'Commercial Bank of Cameroon (CBC)',
            ],
        ],
        'Madagascar' => [
            'code' => 'MG',
            'currency' => 'MGA',
            'banks' => [
                'BNI Madagascar',
                'BMOI (Groupe BCP)',
                'Société Générale Madagasikara',
                'Bank of Africa Madagascar',
                'MCB Madagascar',
                'BGFIBank Madagascar',
            ],
        ],
    ];

    public static function countryNames(): array
    {
        $names = array_keys(self::COUNTRIES);

        return array_combine($names, $names);
    }

    public static function banksForCountry(?string $country): array
    {
        if (! $country || ! isset(self::COUNTRIES[$country])) {
            return [
                'Banque Nationale',
                'Banco Central de Operaciones',
                'Banque Internationale',
                'Crédit Mutuel International',
                'Banque Commerciale Partenaire',
            ];
        }

        return self::COUNTRIES[$country]['banks'];
    }

    public static function currencyForCountry(?string $country): string
    {
        if (! $country || ! isset(self::COUNTRIES[$country])) {
            return 'EUR';
        }

        return self::COUNTRIES[$country]['currency'];
    }

    public static function localeForCountry(?string $country): string
    {
        if (! $country) {
            return config('venatomar.default_locale', 'es');
        }

        $french = ['France', 'Belgique', 'Sénégal', 'Côte d’Ivoire', 'Bénin', 'Cameroun', 'Madagascar'];
        if (in_array($country, $french, true)) {
            return 'fr';
        }

        $english = ['United Kingdom', 'United States', 'Canada'];
        if (in_array($country, $english, true)) {
            return 'en';
        }

        if ($country === 'Deutschland') {
            return 'de';
        }
        if ($country === 'Italia') {
            return 'it';
        }
        if ($country === 'Portugal') {
            return 'pt';
        }

        return 'es';
    }

    public static function codeForCountry(?string $country): string
    {
        if (! $country || ! isset(self::COUNTRIES[$country])) {
            return 'ES';
        }

        return self::COUNTRIES[$country]['code'];
    }

    public static function countryNameByCode(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        $codeUpper = strtoupper(trim($code));
        foreach (self::COUNTRIES as $name => $data) {
            if (strtoupper($data['code'] ?? '') === $codeUpper) {
                return $name;
            }
        }

        $configName = config('countries.'.strtolower($code).'.name');
        if ($configName) {
            return $configName;
        }

        return $codeUpper;
    }

    public static function countryOptions(): array
    {
        $options = [];
        foreach (self::COUNTRIES as $name => $data) {
            $options[strtolower($data['code'])] = $name.' ('.strtoupper($data['code']).')';
        }

        foreach (config('countries', []) as $code => $data) {
            $codeLower = strtolower($code);
            if (! isset($options[$codeLower])) {
                $options[$codeLower] = ($data['name'] ?? strtoupper($code)).' ('.strtoupper($code).')';
            }
        }

        asort($options);

        return $options;
    }

    public static function generateIban(string $countryCode = 'ES'): string
    {
        $code = strtoupper(substr($countryCode, 0, 2));
        $check = sprintf('%02d', mt_rand(10, 98));
        $bank = sprintf('%04d', mt_rand(1000, 9999));
        $branch = sprintf('%04d', mt_rand(1000, 9999));
        $account = sprintf('%010d', mt_rand(1000000000, 9999999999));

        return "{$code}{$check} {$bank} {$branch} {$account}";
    }
}
