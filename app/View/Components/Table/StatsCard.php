<?php

namespace App\View\Components\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatsCard extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $title, $icon, $bgColor, $class;
    
    public function __construct($id, $title, $icon, $bgColor, $class)
    {
        //
        $this->id = $id;
        $this->title = $title;
        $this->icon = $icon;
        $this->bgColor = $bgColor;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table.stats-card');
    }
}
