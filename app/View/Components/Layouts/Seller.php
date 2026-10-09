<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;
use Illuminate\View\View;

class Seller extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $header = null
    ) {}

    public function render(): View
    {
        return view('seller.layouts.seller');
    }
}
