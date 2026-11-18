{{--
    EXACT list of cookies set by the site (pitfall 6). Third-party rows only
    appear when the matching ID is configured in .env (tags load after consent).
    $t = translated strings supplied by resources/views/legal/{locale}/cookie-policy.blade.php
--}}
@php
    $minutes = $legal->sessionMinutes();
    $rows = [
        [$legal->sessionCookie(), $t['session'], str_replace(':n', $minutes, $t['minutes']), $t['own'], $t['necessary']],
        ['XSRF-TOKEN', $t['xsrf'], str_replace(':n', $minutes, $t['minutes']), $t['own'], $t['necessary']],
        ['pf_consent', $t['consent'], str_replace(':n', 365, $t['days']), $t['own'], $t['necessary']],
        ['pf_utm', $t['utm'], str_replace(':n', $legal->utmDays(), $t['days']), $t['own'], $t['statistics']],
    ];
    if ($legal->trackingEnabled('ga4_id')) {
        $rows[] = ['_ga, _ga_*', $t['ga'], $t['provider'], 'Google', $t['statistics']];
    }
    if ($legal->trackingEnabled('google_ads_id')) {
        $rows[] = ['_gcl_au', $t['ads'], $t['provider'], 'Google', $t['marketing']];
    }
    if ($legal->trackingEnabled('meta_pixel_id')) {
        $rows[] = ['_fbp', $t['meta'], $t['provider'], 'Meta', $t['marketing']];
    }
@endphp
<div class="overflow-x-auto">
    <table>
        <thead>
            <tr>@foreach ($t['h'] as $h)<th scope="col">{{ $h }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td><code>{!! $row[0] !!}</code></td>
                    <td>{{ $row[1] }}</td>
                    <td>{{ $row[2] }}</td>
                    <td>{{ $row[3] }}</td>
                    <td>{{ $row[4] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@unless ($legal->trackingEnabled('ga4_id') || $legal->trackingEnabled('google_ads_id') || $legal->trackingEnabled('meta_pixel_id'))
    <p>{{ $t['none'] }}</p>
@endunless
