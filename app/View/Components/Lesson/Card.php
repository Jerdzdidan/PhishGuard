<?php

namespace App\View\Components\Lesson;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    /**
     * Create a new component instance.
     */

    public $title, $route, $img, $description, $time, $difficulty;

    public function __construct($title, $route = '', $img = '', $description = '', $time = '', $difficulty = '')
    {
        $this->title = $title;
        $this->route = $route;
        $this->img = $img;
        $this->description = $description;
        $this->time = $time;
        $this->difficulty = $difficulty;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.lesson.card');
    }
}
