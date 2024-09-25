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
        public string $color = 'blue-600', // Color of the spinner
        public string $backgroundColor = 'gray-200', // Background color of the spinner
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.loading');
    }
}
