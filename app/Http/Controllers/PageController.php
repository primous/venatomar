<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Support\Country;
use App\Support\Venatomar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(Country $country): View
    {
        $faq = collect(['what', 'bureau', 'amounts', 'who', 'cost', 'grant'])
            ->map(fn ($k) => [
                'q' => __('home.faq.'.$k.'.q', ['bureau' => $country->creditBureau]),
                'a' => __('home.faq.'.$k.'.a', self::faqParams($country)),
            ])
            // The "amounts" answer needs real bounds: hidden otherwise.
            ->reject(fn ($item, $i) => $i === 2 && self::faqParams($country)['min'] === '')
            ->values();

        return view('pages.home', ['faq' => $faq]);
    }

    public function apply(): View
    {
        return view('pages.apply');
    }

    public function solicitar(): View
    {
        return view('pages.solicitar');
    }

    public function solicitarEmprestimo(): View
    {
        return view('pages.solicitar-emprestimo');
    }

    public function thanks(Request $request): View
    {
        $lead = Lead::find($request->session()->get('last_lead_id'));
        abort_if($lead === null, 404);

        return view('pages.thanks', [
            'reference' => $lead->reference,
            'maskedEmail' => Venatomar::maskEmail($lead->email),
        ]);
    }

    public function solicitarThanks(Request $request): View
    {
        $lead = Lead::find($request->session()->get('last_lead_id'));
        abort_if($lead === null, 404);

        return view('pages.solicitar-thanks', [
            'reference' => $lead->reference,
            'maskedEmail' => Venatomar::maskEmail($lead->email),
        ]);
    }

    public function solicitarEmprestimoThanks(Request $request): View
    {
        $lead = Lead::find($request->session()->get('last_lead_id'));
        abort_if($lead === null, 404);

        return view('pages.solicitar-emprestimo-thanks', [
            'reference' => $lead->reference,
            'maskedEmail' => Venatomar::maskEmail($lead->email),
        ]);
    }

    public function legal(string $page): View
    {
        $pageMap = [
            'terminos-y-condiciones' => 'terms-and-conditions',
            'politica-de-privacidad' => 'privacy-policy',
            'politica-de-cookies' => 'cookie-policy',
        ];
        $targetPage = $pageMap[$page] ?? $page;
        $locale = app()->getLocale();
        $view = 'legal.'.$locale.'.'.$targetPage;

        return view('pages.legal', [
            'page' => $targetPage,
            'content' => view()->exists($view) ? $view : 'legal.es.'.$targetPage,
        ]);
    }

    public function styleguide(Request $request): View
    {
        // Local only (see routes/web.php). ?lang=xx previews another locale.
        $lang = (string) $request->query('lang', 'es');
        \App\Http\Middleware\SetLocale::apply(Venatomar::isLocale($lang) ? $lang : 'es');

        return view('pages.styleguide');
    }

    public static function faqParams(Country $country): array
    {
        $loan = $country->loan();
        $hasBounds = $loan['amount_min'] !== null && $loan['amount_max'] !== null;

        return [
            'bureau' => $country->creditBureau,
            'min' => $hasBounds ? Venatomar::money($loan['amount_min'], $country) : '',
            'max' => $hasBounds ? Venatomar::money($loan['amount_max'], $country) : '',
        ];
    }
}
