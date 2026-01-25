<?php

namespace App\View\Components\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    /**
     * Create a new component instance.
     */

    public $subtitle, $title, $showBackButton, $backUrl;
    
    public function __construct($title, $subtitle, $showBackButton = false, $backUrl = null)
    {
        //
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->showBackButton = $showBackButton;
        $this->backUrl = $backUrl;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table.page-header');
    }
}
