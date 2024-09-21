<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Loading extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public int $size = 7, // Size of the spinner in rem
        public string $color = 'white', // Color of the spinner
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.loading');
    }
}
