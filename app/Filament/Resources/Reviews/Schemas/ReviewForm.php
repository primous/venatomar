<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Support\AdminText as A;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        $locales = array_keys(config('venatomar.locales'));
        $r = fn (string $key) => A::t('review.'.$key);

        return $schema->components([
            Section::make($r('section_review'))->columns(2)->columnSpanFull()->schema([
                TextInput::make('author_name')->label($r('display_name'))->required()->maxLength(80),
                Select::make('locale')->label($r('language'))->required()->default('es')
                    ->options(array_combine($locales, array_map('strtoupper', $locales))),
                Select::make('rating')->label($r('rating'))->required()->default(5)
                    ->options([5 => '★★★★★ 5', 4 => '★★★★ 4', 3 => '★★★ 3', 2 => '★★ 2', 1 => '★ 1']),
                DatePicker::make('reviewed_at')->label($r('date'))->required()->default(now())->native(false)->displayFormat('d/m/Y'),
                TextInput::make('title')->label($r('title'))->maxLength(120)->columnSpanFull(),
                Textarea::make('body')->label($r('text'))->required()->rows(5)->maxLength(2000)->columnSpanFull(),
            ]),
            Section::make($r('section_moderation'))->columns(2)->columnSpanFull()->schema([
                Toggle::make('is_published')->label($r('is_published')),
                Toggle::make('is_verified')->label($r('is_verified'))->helperText($r('verified_help')),
                TextInput::make('source')->label($r('source'))->disabled()->dehydrated(false)->placeholder('admin'),
            ]),
        ]);
    }
}
