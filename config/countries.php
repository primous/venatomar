<?php

/*
|--------------------------------------------------------------------------
| Country configuration (technical and legal data, no commercial figures)
|--------------------------------------------------------------------------
|
| Keys are ISO 3166-1 alpha-2 codes in lower case. Locales map to countries
| in config/venatomar.php ("locales"). Option labels are translated in
| lang/{locale}.json under "opt.{group}.{key}".
|
| phone_regex  : national number, without prefix and without leading 0.
| postal_regex : applied to the upper-cased, trimmed value.
| document     : identity document, validated by App\Rules\NationalId.
| bank         : "iban" (country + length) or "mg" (RIB 23 digits / Mobile Money).
| date_order   : order of the day / month / year inputs.
| address_order: order of the address fields in step 3.
|
*/

$employment = ['employee', 'self_employed', 'civil_servant', 'unemployed', 'benefits', 'retired', 'other'];
$education = ['primary', 'secondary', 'upper_secondary', 'higher_short', 'higher_long'];
$marital = ['single', 'married', 'widowed', 'divorced'];
$home = ['tenant', 'owner_no_mortgage', 'owner_mortgage', 'other'];
$addressDefault = ['geo_zip', 'geo_province', 'geo_city', 'home_street', 'home_number', 'apartment_floor'];
$addressNumberFirst = ['home_number', 'home_street', 'apartment_floor', 'geo_city', 'geo_province', 'geo_zip'];

return [

    'fr' => [
        'name' => 'France', 'currency' => 'EUR', 'number_locale' => 'fr_FR',
        'phone_prefix' => '+33', 'phone_regex' => '/^[67]\d{8}$/', 'phone_example' => '6 12 34 56 78',
        'postal_regex' => '/^\d{5}$/', 'postal_example' => '75008',
        'document' => ['type' => 'fr_cni', 'required' => true, 'regex' => '/^\d{12}$/', 'example' => '123456789012'],
        'bank' => ['type' => 'iban', 'iban_country' => 'FR', 'iban_length' => 27],
        'credit_bureau' => 'Banque de France (FICP)',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '/',
        'address_order' => $addressDefault,
        'plate_regex' => '/^[A-Z]{2}[-\s]?\d{3}[-\s]?[A-Z]{2}$/', 'plate_example' => 'AA-123-AA',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'CNIL', 'url' => 'https://www.cnil.fr'],
    ],

    'es' => [
        'name' => 'España', 'currency' => 'EUR', 'number_locale' => 'es_ES',
        'phone_prefix' => '+34', 'phone_regex' => '/^[67]\d{8}$/', 'phone_example' => '612 345 678',
        'postal_regex' => '/^(0[1-9]|[1-4]\d|5[0-2])\d{3}$/', 'postal_example' => '28001',
        'document' => ['type' => 'es_dni_nie', 'required' => true, 'regex' => '/^(\d{8}[A-Z]|[XYZ]\d{7}[A-Z])$/', 'example' => '12345678Z'],
        'bank' => ['type' => 'iban', 'iban_country' => 'ES', 'iban_length' => 24],
        'credit_bureau' => 'ASNEF / RAI',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '/',
        'address_order' => $addressDefault,
        'plate_regex' => '/^\d{4}[\s-]?[BCDFGHJKLMNPRSTVWXYZ]{3}$/', 'plate_example' => '1234 BCD',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'Agencia Española de Protección de Datos (AEPD)', 'url' => 'https://www.aepd.es'],
    ],

    'gb' => [
        'name' => 'United Kingdom', 'currency' => 'GBP', 'number_locale' => 'en_GB',
        'phone_prefix' => '+44', 'phone_regex' => '/^7\d{9}$/', 'phone_example' => '7400 123456',
        'postal_regex' => '/^(GIR ?0AA|((([A-Z]\d{1,2})|([A-Z][A-HJ-Y]\d{1,2})|([A-Z]\d[A-Z])|([A-Z][A-HJ-Y]\d[A-Z]?)) ?\d[A-Z]{2}))$/', 'postal_example' => 'SW1A 1AA',
        'document' => ['type' => 'gb_nino', 'required' => false, 'regex' => '/^(?!BG|GB|NK|KN|TN|NT|ZZ)[A-CEGHJ-PR-TW-Z][A-CEGHJ-NPR-TW-Z]\d{6}[A-D]$/', 'example' => 'QQ123456C'],
        'bank' => ['type' => 'iban', 'iban_country' => 'GB', 'iban_length' => 22],
        'credit_bureau' => 'CCJ / defaults',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '/',
        'address_order' => $addressNumberFirst,
        'plate_regex' => '/^[A-Z]{2}\d{2}\s?[A-Z]{3}$/', 'plate_example' => 'AB12 CDE',
        'employment' => $employment, 'education' => $education, 'marital' => [...$marital, 'civil_partnership'], 'home' => $home,
        'authority' => ['name' => "Information Commissioner's Office (ICO)", 'url' => 'https://ico.org.uk'],
    ],

    'de' => [
        'name' => 'Deutschland', 'currency' => 'EUR', 'number_locale' => 'de_DE',
        'phone_prefix' => '+49', 'phone_regex' => '/^1[5-7]\d{8,9}$/', 'phone_example' => '151 23456789',
        'postal_regex' => '/^\d{5}$/', 'postal_example' => '10115',
        'document' => null, // not collected in Germany
        'bank' => ['type' => 'iban', 'iban_country' => 'DE', 'iban_length' => 22],
        'credit_bureau' => 'SCHUFA',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '.',
        'address_order' => ['home_street', 'home_number', 'apartment_floor', 'geo_zip', 'geo_city', 'geo_province'],
        'plate_regex' => '/^[A-ZÄÖÜ]{1,3}[\s-]?[A-Z]{1,2}[\s-]?\d{1,4}[EH]?$/u', 'plate_example' => 'B-AB 1234',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'Bundesbeauftragte für den Datenschutz und die Informationsfreiheit (BfDI) / Landesdatenschutzbehörden', 'url' => 'https://www.bfdi.bund.de'],
    ],

    'it' => [
        'name' => 'Italia', 'currency' => 'EUR', 'number_locale' => 'it_IT',
        'phone_prefix' => '+39', 'phone_regex' => '/^3\d{8,9}$/', 'phone_example' => '312 345 6789',
        'postal_regex' => '/^\d{5}$/', 'postal_example' => '00184',
        'document' => ['type' => 'it_codice_fiscale', 'required' => true, 'regex' => '/^[A-Z]{6}[0-9LMNPQRSTUV]{2}[ABCDEHLMPRST][0-9LMNPQRSTUV]{2}[A-Z][0-9LMNPQRSTUV]{3}[A-Z]$/', 'example' => 'RSSMRA85T10A562S'],
        'bank' => ['type' => 'iban', 'iban_country' => 'IT', 'iban_length' => 27],
        'credit_bureau' => 'CRIF',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '/',
        'address_order' => ['home_street', 'home_number', 'apartment_floor', 'geo_zip', 'geo_city', 'geo_province'],
        'plate_regex' => '/^[A-Z]{2}\s?\d{3}\s?[A-Z]{2}$/', 'plate_example' => 'AB 123 CD',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'Garante per la protezione dei dati personali', 'url' => 'https://www.garanteprivacy.it'],
    ],

    'nl' => [
        'name' => 'Nederland', 'currency' => 'EUR', 'number_locale' => 'nl_NL',
        'phone_prefix' => '+31', 'phone_regex' => '/^6\d{8}$/', 'phone_example' => '6 12345678',
        'postal_regex' => '/^\d{4}\s?[A-Z]{2}$/', 'postal_example' => '1012 AB',
        'document' => null, // the BSN must NEVER be collected
        'bank' => ['type' => 'iban', 'iban_country' => 'NL', 'iban_length' => 18],
        'credit_bureau' => 'BKR',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '-',
        'address_order' => ['geo_zip', 'home_number', 'apartment_floor', 'home_street', 'geo_city', 'geo_province'],
        'plate_regex' => '/^(?=(?:[A-Z0-9]-?){6}$)[A-Z0-9]{1,3}-?[A-Z0-9]{1,3}-?[A-Z0-9]{1,3}$/', 'plate_example' => 'AB-123-C',
        'employment' => $employment, 'education' => $education, 'marital' => [...$marital, 'registered_partnership'], 'home' => $home,
        'authority' => ['name' => 'Autoriteit Persoonsgegevens (AP)', 'url' => 'https://www.autoriteitpersoonsgegevens.nl'],
    ],

    'gr' => [
        'name' => 'Ελλάδα', 'currency' => 'EUR', 'number_locale' => 'el_GR',
        'phone_prefix' => '+30', 'phone_regex' => '/^69\d{8}$/', 'phone_example' => '691 234 5678',
        'postal_regex' => '/^\d{3}\s?\d{2}$/', 'postal_example' => '105 57',
        'document' => ['type' => 'gr_afm', 'required' => true, 'regex' => '/^\d{9}$/', 'example' => '123456783'],
        'bank' => ['type' => 'iban', 'iban_country' => 'GR', 'iban_length' => 27],
        'credit_bureau' => 'Τειρεσίας',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '/',
        'address_order' => ['home_street', 'home_number', 'apartment_floor', 'geo_zip', 'geo_city', 'geo_province'],
        'plate_regex' => '/^[ABEHIKMNOPTXYZΑΒΕΖΗΙΚΜΝΟΡΤΥΧ]{3}[\s-]?\d{4}$/u', 'plate_example' => 'ΙΚΥ-1234',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'Αρχή Προστασίας Δεδομένων Προσωπικού Χαρακτήρα (HDPA)', 'url' => 'https://www.dpa.gr'],
    ],

    'pt' => [
        'name' => 'Portugal', 'currency' => 'EUR', 'number_locale' => 'pt_PT',
        'phone_prefix' => '+351', 'phone_regex' => '/^9[1236]\d{7}$/', 'phone_example' => '912 345 678',
        'postal_regex' => '/^\d{4}-\d{3}$/', 'postal_example' => '1100-148',
        'document' => ['type' => 'pt_nif', 'required' => true, 'regex' => '/^\d{9}$/', 'example' => '123456789'],
        'bank' => ['type' => 'iban', 'iban_country' => 'PT', 'iban_length' => 25],
        'credit_bureau' => 'Central de Responsabilidades de Crédito (Banco de Portugal)',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '/',
        'address_order' => ['home_street', 'home_number', 'apartment_floor', 'geo_zip', 'geo_city', 'geo_province'],
        'plate_regex' => '/^([A-Z]{2}-?\d{2}-?[A-Z]{2}|\d{2}-?\d{2}-?[A-Z]{2}|\d{2}-?[A-Z]{2}-?\d{2}|[A-Z]{2}-?\d{2}-?\d{2})$/', 'plate_example' => 'AA-00-AA',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'Comissão Nacional de Proteção de Dados (CNPD)', 'url' => 'https://www.cnpd.pt'],
    ],

    'sk' => [
        'name' => 'Slovensko', 'currency' => 'EUR', 'number_locale' => 'sk_SK',
        'phone_prefix' => '+421', 'phone_regex' => '/^9\d{8}$/', 'phone_example' => '912 345 678',
        'postal_regex' => '/^\d{3}\s?\d{2}$/', 'postal_example' => '811 01',
        'document' => ['type' => 'sk_rodne_cislo', 'required' => true, 'regex' => '/^\d{6}\/?\d{3,4}$/', 'example' => '900101/1239'],
        'bank' => ['type' => 'iban', 'iban_country' => 'SK', 'iban_length' => 24],
        'credit_bureau' => 'SRBI / NRKI',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '.',
        'address_order' => ['home_street', 'home_number', 'apartment_floor', 'geo_zip', 'geo_city', 'geo_province'],
        'plate_regex' => '/^[A-Z]{2}[\s-]?\d{3}[A-Z]{2}$/', 'plate_example' => 'BA-123AB',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'Úrad na ochranu osobných údajov SR (ÚOOÚ)', 'url' => 'https://dataprotection.gov.sk'],
    ],

    'si' => [
        'name' => 'Slovenija', 'currency' => 'EUR', 'number_locale' => 'sl_SI',
        'phone_prefix' => '+386', 'phone_regex' => '/^[3-7]\d{7}$/', 'phone_example' => '41 234 567',
        'postal_regex' => '/^\d{4}$/', 'postal_example' => '1000',
        'document' => ['type' => 'si_emso', 'required' => true, 'regex' => '/^\d{13}$/', 'example' => '0101990500003'],
        'bank' => ['type' => 'iban', 'iban_country' => 'SI', 'iban_length' => 19],
        'credit_bureau' => 'SISBON',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '.',
        'address_order' => ['home_street', 'home_number', 'apartment_floor', 'geo_zip', 'geo_city', 'geo_province'],
        'plate_regex' => '/^[A-ZČŠŽ]{2}[\s-]?[A-Z0-9]{2,3}[\s-]?[A-Z0-9]{2,3}$/u', 'plate_example' => 'LJ AB-123',
        'employment' => $employment, 'education' => $education, 'marital' => $marital, 'home' => $home,
        'authority' => ['name' => 'Informacijski pooblaščenec RS (IP-RS)', 'url' => 'https://www.ip-rs.si'],
    ],

    'mg' => [
        'name' => 'Madagasikara', 'currency' => 'MGA', 'number_locale' => 'mg_MG',
        'phone_prefix' => '+261', 'phone_regex' => '/^3[2348]\d{7}$/', 'phone_example' => '34 12 345 67',
        'postal_regex' => '/^\d{3}$/', 'postal_example' => '101',
        'document' => ['type' => 'mg_cin', 'required' => true, 'regex' => '/^\d{12}$/', 'example' => '101011234567'],
        'bank' => ['type' => 'mg'],
        'mobile_money_operators' => ['mvola' => 'MVola', 'orange_money' => 'Orange Money', 'airtel_money' => 'Airtel Money'],
        'credit_bureau' => 'Centrale des risques (BFM)',
        'date_order' => ['d', 'm', 'y'], 'date_separator' => '/',
        'address_order' => $addressDefault,
        'street_label' => 'lot', // the street field becomes "Lot / Quartier"
        'vehicle_includes_motorbike' => true,
        'plate_regex' => '/^\d{4}\s?[A-Z]{2,3}$/', 'plate_example' => '1234 TBA',
        'employment' => [...$employment, 'informal'], 'education' => $education, 'marital' => $marital, 'home' => [...$home, 'family'],
        'authority' => ['name' => "Commission Malagasy sur l'Informatique et les Libertés (CMIL)", 'url' => 'https://www.cmil.mg'],
    ],

];
