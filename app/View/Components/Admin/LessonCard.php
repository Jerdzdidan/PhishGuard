<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LessonCard extends Component
{
    /**
     * Create a new component instance.
     */
    public $title, $route, $img, $description, $time, $difficulty, $status;

    public function __construct($title, $route = '', $img = '', $description = '', $time = '', $difficulty = '', $status)
    {
        $this->title = $title;
        $this->route = $route;
        $this->img = $img;
        $this->description = $description;
        $this->time = $time;
        $this->difficulty = $difficulty;
        $this->status = $status;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.lesson-card');
    }
}
