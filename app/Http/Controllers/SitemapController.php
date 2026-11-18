<?php

namespace App\Http\Controllers;

use App\Support\Venatomar;
use App\Support\Seo;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $pages = [
            ['home', []],
            ['apply', []],
            ['legal', ['page' => 'terms-and-conditions']],
            ['legal', ['page' => 'privacy-policy']],
            ['legal', ['page' => 'cookie-policy']],
        ];
        $locales = array_keys(Venatomar::locales());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";
        foreach ($pages as [$name, $params]) {
            foreach ($locales as $locale) {
                $xml .= '  <url><loc>'.e(route($name, ['locale' => $locale] + $params)).'</loc>';
                foreach ($locales as $alt) {
                    $xml .= '<xhtml:link rel="alternate" hreflang="'.Seo::hreflang($alt).'" href="'.e(route($name, ['locale' => $alt] + $params)).'"/>';
                }
                $xml .= '<xhtml:link rel="alternate" hreflang="x-default" href="'.e(route($name, ['locale' => config('venatomar.default_locale')] + $params)).'"/>';
                $xml .= "</url>\n";
            }
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $lines = ['User-agent: *'];
        foreach (array_keys(Venatomar::locales()) as $locale) {
            $lines[] = "Disallow: /$locale/my-account";
            $lines[] = "Disallow: /$locale/apply-loan/gracias";
        }
        $lines[] = 'Disallow: /livewire/';
        $lines[] = '';
        $lines[] = 'Sitemap: '.route('sitemap');

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
