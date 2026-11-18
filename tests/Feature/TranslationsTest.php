<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/** Guards rule 5 (no hard-coded text): every key used exists, every language is complete. */
class TranslationsTest extends TestCase
{
    private function keys(string $locale): array
    {
        $data = json_decode(File::get(lang_path($locale.'.json')), true, flags: JSON_THROW_ON_ERROR);

        return array_keys($data);
    }

    public function test_every_language_has_exactly_the_spanish_keys(): void
    {
        $reference = $this->keys('es');
        sort($reference);
        foreach (array_keys(config('venatomar.locales')) as $locale) {
            $this->assertFileExists(lang_path($locale.'.json'), "lang/$locale.json is missing");
            $keys = $this->keys($locale);
            sort($keys);
            $this->assertSame([], array_values(array_diff($reference, $keys)), "Missing keys in $locale.json");
            $this->assertSame([], array_values(array_diff($keys, $reference)), "Unknown keys in $locale.json");
        }
    }

    public function test_every_literal_key_used_in_code_exists_in_spanish(): void
    {
        $reference = array_flip($this->keys('es'));
        $files = array_merge(
            File::allFiles(resource_path('views')),
            File::allFiles(app_path()),
        );
        $missing = [];
        foreach ($files as $file) {
            // Literal keys only (a key followed by "." is a concatenated prefix).
            preg_match_all("/(?:__|trans_choice)\\(\\s*'([a-z0-9_.\\-]+)'(?!\\s*\\.)/", $file->getContents(), $m);
            foreach ($m[1] as $key) {
                if (! str_ends_with($key, '.') && ! isset($reference[$key])) {
                    $missing[] = $key.' ('.$file->getFilename().')';
                }
            }
        }
        $this->assertSame([], array_values(array_unique($missing)));
    }

    public function test_every_dynamic_option_key_exists(): void
    {
        $reference = array_flip($this->keys('es'));
        foreach (array_keys(config('countries')) as $cc) {
            $country = new \App\Support\Country($cc);
            foreach (['employment', 'education', 'marital', 'home', 'gender', 'loan_purpose', 'company_sector', 'payout_method'] as $group) {
                foreach (array_keys($country->options($group)) as $key) {
                    $this->assertArrayHasKey("opt.$group.$key", $reference);
                }
            }
            $this->assertArrayHasKey("form.geo_province.$cc", $reference);
            if ($country->hasDocument()) {
                $this->assertArrayHasKey("form.document.$cc", $reference);
            }
        }
    }

    public function test_legal_pages_exist_for_every_locale(): void
    {
        foreach (array_keys(config('venatomar.locales')) as $locale) {
            foreach (['terms-and-conditions', 'privacy-policy', 'cookie-policy'] as $page) {
                $this->assertFileExists(resource_path("views/legal/$locale/$page.blade.php"));
            }
        }
    }
}
