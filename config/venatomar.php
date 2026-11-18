<?php

/*
|--------------------------------------------------------------------------
| Venatomar — business configuration (single source of truth)
|--------------------------------------------------------------------------
|
| RULE: no commercial figure is ever invented. Every value written between
| square brackets ("[...]") is a placeholder: App\Support\Venatomar treats
| it as "absent", and any block that depends on it is hidden.
| Replace placeholders with real, verified values before going live.
|
*/

return [

    'brand' => 'Venatomar',

    // Where every new loan request is delivered (internal e-mail).
    'lead_recipient' => env('VENATOMAR_LEAD_RECIPIENT', 'info@venatomar.com'),

    // Legal identity (footer, legal pages, e-mails).
    'legal' => [
        'company_name' => env('VENATOMAR_COMPANY_NAME', 'Leads for Finance S.L.U'),
        'legal_form' => env('VENATOMAR_LEGAL_FORM', 'S.L.U'),
        'tax_id' => env('VENATOMAR_TAX_ID', 'B67406025'),
        'trade_register' => env('VENATOMAR_TRADE_REGISTER', 'tome 46865, folio 53, feuille B533516'),
        'address' => env('VENATOMAR_ADDRESS', 'Carrer d’Estruc, 9, 08002 Barcelone'),
        'contact_email' => env('VENATOMAR_CONTACT_EMAIL', 'info@venatomar.com'),
        'credit_intermediary_number' => env('VENATOMAR_CREDIT_INTERMEDIARY_NUMBER', 'B12345678'),
        'partners' => '[LISTE_PARTENAIRES]',
        'dpo_email' => env('VENATOMAR_DPO_EMAIL', 'dpo@venatomar.com'),
        'jurisdiction' => env('VENATOMAR_JURISDICTION', 'Barcelone'),
        'retention' => '24 meses después de la última solicitud',          // e.g. "24 months after the last request"
        'hosting_provider' => 'Hostinger International Ltd.',
    ],

    // Bump when the privacy / cookie policy changes: stored with each consent.
    'consent_version' => '2026-11-15',
    'cookie_policy_version' => '2026-11-15',

    // Reviews: the rating badge is only shown when enabled AND values are filled.
    'reviews' => [
        // One e-mail per applicant, `delay_days` after their request, inviting them to leave a review.
        // Never repeated; requests older than delay_days + window_days are skipped.
        'invitation' => ['enabled' => true, 'delay_days' => 7, 'window_days' => 30],
        'enabled' => false,
        'rating' => '[NOTE_MOYENNE]',
        'count' => '[NOMBRE_AVIS]',
        'source' => '[SOURCE_AVIS]',
        'per_page' => 6,
        // Short proof lines shown next to the average (e.g. monthly volume). REAL figures only.
        'highlights' => [],
    ],

    /*
    | Loan parameters per country (ISO 3166-1 alpha-2, lower case).
    | amount_* in the country currency, term_* in months, APR (TAE) in %.
    | example = representative example (all fields required, else hidden).
    | debt_threshold = amount used in the question "¿Tus deudas superan X?".
    */
    /*
    | Home calculator slider range, per currency. Used ONLY to draw the sliders
    | when the country's loan bounds above are still placeholders; once
    | amount_min/max and term_min/max are filled, those values win.
    | Values from the client's mock-up, confirmed by the client on 2026-11-16.
    */
    'calculator' => [
        'EUR' => ['amount_min' => 100, 'amount_max' => 5000, 'amount_step' => 50, 'amount_default' => 1400, 'term_min' => 3, 'term_max' => 12, 'term_default' => 7],
        'USD' => ['amount_min' => 100, 'amount_max' => 5000, 'amount_step' => 50, 'amount_default' => 1400, 'term_min' => 3, 'term_max' => 12, 'term_default' => 7],
        'UYU' => ['amount_min' => 5000, 'amount_max' => 200000, 'amount_step' => 1000, 'amount_default' => 50000, 'term_min' => 3, 'term_max' => 24, 'term_default' => 12],
        'GBP' => ['amount_min' => 100, 'amount_max' => 5000, 'amount_step' => 50, 'amount_default' => 1400, 'term_min' => 3, 'term_max' => 12, 'term_default' => 7],
        'MGA' => ['amount_min' => 100000, 'amount_max' => 5000000, 'amount_step' => 50000, 'amount_default' => 1400000, 'term_min' => 3, 'term_max' => 12, 'term_default' => 7],
    ],

    'loan' => [
        'es' => [
            'amount_min' => '[MONTANT_MIN_ES]', 'amount_max' => '[MONTANT_MAX_ES]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_ES]', 'term_max' => '[DUREE_MAX_ES]',
            'apr_min' => '[TAE_MIN_ES]', 'apr_max' => '[TAE_MAX_ES]',
            'example' => ['amount' => '[EX_MONTANT_ES]', 'term' => '[EX_DUREE_ES]', 'net' => '[EX_NET_ES]', 'total' => '[EX_TOTAL_ES]', 'apr' => '[EX_TAE_ES]'],
            'debt_threshold' => 8000,
        ],
        'gb' => [
            'amount_min' => '[MONTANT_MIN_GB]', 'amount_max' => '[MONTANT_MAX_GB]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_GB]', 'term_max' => '[DUREE_MAX_GB]',
            'apr_min' => '[TAE_MIN_GB]', 'apr_max' => '[TAE_MAX_GB]',
            'example' => ['amount' => '[EX_MONTANT_GB]', 'term' => '[EX_DUREE_GB]', 'net' => '[EX_NET_GB]', 'total' => '[EX_TOTAL_GB]', 'apr' => '[EX_TAE_GB]'],
            'debt_threshold' => '[SEUIL_DETTE_GB]',
        ],
        'de' => [
            'amount_min' => '[MONTANT_MIN_DE]', 'amount_max' => '[MONTANT_MAX_DE]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_DE]', 'term_max' => '[DUREE_MAX_DE]',
            'apr_min' => '[TAE_MIN_DE]', 'apr_max' => '[TAE_MAX_DE]',
            'example' => ['amount' => '[EX_MONTANT_DE]', 'term' => '[EX_DUREE_DE]', 'net' => '[EX_NET_DE]', 'total' => '[EX_TOTAL_DE]', 'apr' => '[EX_TAE_DE]'],
            'debt_threshold' => '[SEUIL_DETTE_DE]',
        ],
        'it' => [
            'amount_min' => '[MONTANT_MIN_IT]', 'amount_max' => '[MONTANT_MAX_IT]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_IT]', 'term_max' => '[DUREE_MAX_IT]',
            'apr_min' => '[TAE_MIN_IT]', 'apr_max' => '[TAE_MAX_IT]',
            'example' => ['amount' => '[EX_MONTANT_IT]', 'term' => '[EX_DUREE_IT]', 'net' => '[EX_NET_IT]', 'total' => '[EX_TOTAL_IT]', 'apr' => '[EX_TAE_IT]'],
            'debt_threshold' => '[SEUIL_DETTE_IT]',
        ],
        'nl' => [
            'amount_min' => '[MONTANT_MIN_NL]', 'amount_max' => '[MONTANT_MAX_NL]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_NL]', 'term_max' => '[DUREE_MAX_NL]',
            'apr_min' => '[TAE_MIN_NL]', 'apr_max' => '[TAE_MAX_NL]',
            'example' => ['amount' => '[EX_MONTANT_NL]', 'term' => '[EX_DUREE_NL]', 'net' => '[EX_NET_NL]', 'total' => '[EX_TOTAL_NL]', 'apr' => '[EX_TAE_NL]'],
            'debt_threshold' => '[SEUIL_DETTE_NL]',
        ],
        'gr' => [
            'amount_min' => '[MONTANT_MIN_GR]', 'amount_max' => '[MONTANT_MAX_GR]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_GR]', 'term_max' => '[DUREE_MAX_GR]',
            'apr_min' => '[TAE_MIN_GR]', 'apr_max' => '[TAE_MAX_GR]',
            'example' => ['amount' => '[EX_MONTANT_GR]', 'term' => '[EX_DUREE_GR]', 'net' => '[EX_NET_GR]', 'total' => '[EX_TOTAL_GR]', 'apr' => '[EX_TAE_GR]'],
            'debt_threshold' => '[SEUIL_DETTE_GR]',
        ],
        'pt' => [
            'amount_min' => '[MONTANT_MIN_PT]', 'amount_max' => '[MONTANT_MAX_PT]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_PT]', 'term_max' => '[DUREE_MAX_PT]',
            'apr_min' => '[TAE_MIN_PT]', 'apr_max' => '[TAE_MAX_PT]',
            'example' => ['amount' => '[EX_MONTANT_PT]', 'term' => '[EX_DUREE_PT]', 'net' => '[EX_NET_PT]', 'total' => '[EX_TOTAL_PT]', 'apr' => '[EX_TAE_PT]'],
            'debt_threshold' => '[SEUIL_DETTE_PT]',
        ],
        'sk' => [
            'amount_min' => '[MONTANT_MIN_SK]', 'amount_max' => '[MONTANT_MAX_SK]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_SK]', 'term_max' => '[DUREE_MAX_SK]',
            'apr_min' => '[TAE_MIN_SK]', 'apr_max' => '[TAE_MAX_SK]',
            'example' => ['amount' => '[EX_MONTANT_SK]', 'term' => '[EX_DUREE_SK]', 'net' => '[EX_NET_SK]', 'total' => '[EX_TOTAL_SK]', 'apr' => '[EX_TAE_SK]'],
            'debt_threshold' => '[SEUIL_DETTE_SK]',
        ],
        'si' => [
            'amount_min' => '[MONTANT_MIN_SI]', 'amount_max' => '[MONTANT_MAX_SI]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_SI]', 'term_max' => '[DUREE_MAX_SI]',
            'apr_min' => '[TAE_MIN_SI]', 'apr_max' => '[TAE_MAX_SI]',
            'example' => ['amount' => '[EX_MONTANT_SI]', 'term' => '[EX_DUREE_SI]', 'net' => '[EX_NET_SI]', 'total' => '[EX_TOTAL_SI]', 'apr' => '[EX_TAE_SI]'],
            'debt_threshold' => '[SEUIL_DETTE_SI]',
        ],
        'mg' => [
            'amount_min' => '[MONTANT_MIN_MG]', 'amount_max' => '[MONTANT_MAX_MG]', 'amount_step' => 50000,
            'term_min' => '[DUREE_MIN_MG]', 'term_max' => '[DUREE_MAX_MG]',
            'apr_min' => '[TAE_MIN_MG]', 'apr_max' => '[TAE_MAX_MG]',
            'example' => ['amount' => '[EX_MONTANT_MG]', 'term' => '[EX_DUREE_MG]', 'net' => '[EX_NET_MG]', 'total' => '[EX_TOTAL_MG]', 'apr' => '[EX_TAE_MG]'],
            'debt_threshold' => '[SEUIL_DETTE_MG]',
        ],
        'sv' => [
            'amount_min' => '[MONTANT_MIN_SV]', 'amount_max' => '[MONTANT_MAX_SV]', 'amount_step' => 50,
            'term_min' => '[DUREE_MIN_SV]', 'term_max' => '[DUREE_MAX_SV]',
            'apr_min' => '[TAE_MIN_SV]', 'apr_max' => '[TAE_MAX_SV]',
            'example' => ['amount' => '[EX_MONTANT_SV]', 'term' => '[EX_DUREE_SV]', 'net' => '[EX_NET_SV]', 'total' => '[EX_TOTAL_SV]', 'apr' => '[EX_TAE_SV]'],
            'debt_threshold' => 3000,
        ],
        'uy' => [
            'amount_min' => '[MONTANT_MIN_UY]', 'amount_max' => '[MONTANT_MAX_UY]', 'amount_step' => 1000,
            'term_min' => '[DUREE_MIN_UY]', 'term_max' => '[DUREE_MAX_UY]',
            'apr_min' => '[TAE_MIN_UY]', 'apr_max' => '[TAE_MAX_UY]',
            'example' => ['amount' => '[EX_MONTANT_UY]', 'term' => '[EX_DUREE_UY]', 'net' => '[EX_NET_UY]', 'total' => '[EX_TOTAL_UY]', 'apr' => '[EX_TAE_UY]'],
            'debt_threshold' => 120000,
        ],
    ],

    // Anti-spam.
    'antispam' => [
        'min_seconds' => 4,
        'per_ip_per_hour' => 5,
        'per_email_per_day' => 3,
        'magic_link_per_ip_per_hour' => 5,
        'reviews_per_ip_per_day' => 3,
    ],

    'magic_link_ttl_minutes' => 15,

    // UTM attribution cookie (only set after statistics consent).
    'utm_cookie_days' => 30,

    // Third-party tags: loaded ONLY after consent, disabled when empty.
    'tracking' => [
        'ga4_id' => env('VENATOMAR_GA4_ID'),        // statistics
        'google_ads_id' => env('VENATOMAR_GADS_ID'), // marketing
        'meta_pixel_id' => env('VENATOMAR_META_PIXEL_ID'), // marketing
    ],

    // Supported locales => country code (see config/countries.php).
    'locales' => [
        'es' => 'es', 'sv' => 'sv', 'uy' => 'uy', 'en' => 'gb', 'de' => 'de', 'it' => 'it', 'nl' => 'nl',
        'el' => 'gr', 'pt' => 'pt', 'sk' => 'sk', 'sl' => 'si', 'mg' => 'mg',
    ],
    'default_locale' => 'es',
];
