<?php

namespace App\Livewire;

use App\Models\Review;
use Livewire\Attributes\Locked;
use Livewire\Component;

/** Real published reviews only; "show more" loads 6 more each time. */
class ReviewsList extends Component
{
    #[Locked]
    public string $locale = 'es';

    public int $limit = 6;

    public function mount(string $locale): void
    {
        $this->locale = $locale;
        $this->limit = (int) config('venatomar.reviews.per_page', 6);
    }

    public function more(): void
    {
        $this->limit += (int) config('venatomar.reviews.per_page', 6);
    }

    public function render()
    {
        $query = Review::published($this->locale);
        $total = (clone $query)->count();

        return view('livewire.reviews-list', [
            'reviews' => $query->limit($this->limit)->get(),
            'hasMore' => $total > $this->limit,
        ]);
    }
}
