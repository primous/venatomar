<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Filament\Resources\DeletionRequests\DeletionRequestResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\DeletionRequest;
use App\Models\Lead;
use App\Models\Review;
use App\Support\AdminText as A;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VenatomarStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $perDay = collect(range(13, 0))->map(fn ($d) => Lead::whereDate('created_at', today()->subDays($d))->count())->all();

        return [
            Stat::make(A::t('stats.today'), Lead::whereDate('created_at', today())->count())
                ->description(A::t('stats.last7', ['count' => Lead::where('created_at', '>=', now()->subDays(7))->count()]))
                ->chart($perDay)->color('success')->url(LeadResource::getUrl()),
            Stat::make(A::t('stats.to_process'), Lead::where('status', LeadStatus::Received)->count())
                ->description(A::t('stats.to_process_desc'))->color('warning')->url(LeadResource::getUrl()),
            Stat::make(A::t('stats.reviews_pending'), Review::where('is_published', false)->count())
                ->description(A::t('stats.reviews_published', ['count' => Review::where('is_published', true)->count()]))
                ->url(ReviewResource::getUrl()),
            Stat::make(A::t('stats.mails_failed'), Lead::whereNotNull('mail_failed_at')->count())
                ->description(A::t('stats.deletions_pending', ['count' => DeletionRequest::where('status', 'pending')->count()]))
                ->color('danger')->url(DeletionRequestResource::getUrl()),
        ];
    }
}
