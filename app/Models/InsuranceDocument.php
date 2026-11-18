<?php

namespace App\Models;

use App\Support\InsurancePdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;

/** Personalised credit-insurance certificate or information sheet generated from the back office. */
class InsuranceDocument extends Model
{
    public const GUARANTEES = ['death', 'ptia', 'itt', 'ipt', 'ipp', 'job_loss'];

    protected $guarded = ['id'];

    protected $hidden = ['document_number'];

    protected function casts(): array
    {
        return [
            'guarantees' => 'array',
            'amount' => 'integer',
            'document_fee' => 'decimal:2',
            'term_months' => 'integer',
            'coverage_percent' => 'integer',
            'document_number' => 'encrypted',
            'birth_date' => 'date',
            'effective_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (InsuranceDocument $doc) {
            $doc->reference = sprintf('VN-AS-%s-%06d', $doc->created_at->format('Y'), $doc->id);
            if (! $doc->effective_date) {
                $doc->effective_date = $doc->created_at->toDateString();
            }
            if (! $doc->document_type) {
                $doc->document_type = 'certificate';
            }
            $doc->saveQuietly();
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expiryDate(): ?Carbon
    {
        $start = $this->effective_date ? Carbon::parse($this->effective_date) : ($this->created_at ?? now());

        return $start->copy()->addMonths($this->term_months ?: 12);
    }

    public function authCode(): string
    {
        $hash = strtoupper(hash('crc32b', ($this->reference ?: 'VN').$this->created_at.$this->amount));

        return sprintf('VN-%s-%04d', $hash, $this->id % 10000);
    }

    public function formatCurrency(int|float|null $amount): string
    {
        if ($amount === null) {
            return '—';
        }

        $fmt = new NumberFormatter(InsurancePdf::NUMBER_LOCALES[$this->locale] ?? 'es_ES', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

        return str_replace(["\u{202F}", "\u{00A0}"], ' ', $fmt->formatCurrency($amount, $this->currency));
    }

    public function formattedDocumentFee(): string
    {
        if ($this->document_fee === null || (float) $this->document_fee <= 0) {
            return '—';
        }

        return $this->formatCurrency((float) $this->document_fee);
    }

    /** Amount in the document's currency, formatted the way the document's language writes numbers. */
    public function money(): string
    {
        $fmt = new NumberFormatter(InsurancePdf::NUMBER_LOCALES[$this->locale] ?? 'es_ES', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

        // Plain spaces: the PDF font has no narrow no-break space.
        return str_replace(["\u{202F}", "\u{00A0}"], ' ', $fmt->formatCurrency($this->amount, $this->currency));
    }

    public function filename(): string
    {
        $tag = $this->document_type === 'information' ? 'Info' : 'Certificat';

        return 'Venatomar-'.$tag.'-'.$this->reference.'-'.strtoupper($this->locale).'.pdf';
    }
}
