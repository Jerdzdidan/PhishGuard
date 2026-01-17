<?php

namespace App\View\Components\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextAreaField extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $label, $placeholder, $rows, $icon;

    public function __construct($id, $label, $placeholder = '', $rows = 2, $icon = '')
    {
        //
        $this->id = $id;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->rows = $rows;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input.text-area-field');
    }
}
