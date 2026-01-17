<?php

namespace App\View\Components\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class IntegerField extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $label, $name, $icon, $value, $placeholder, $min, $max, $help, $required, $step;

    public function __construct($id, $label, $name = null, $icon = null, $value = null, $placeholder = null, $min = null, $max = null, $help = null, $required = false, $step = 1)
    {
        //
        $this->id = $id;
        $this->label = $label;
        $this->name = $name ?? $id;
        $this->icon = $icon;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->min = $min;
        $this->max = $max;
        $this->help = $help;
        $this->required = $required;
        $this->step = $step;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input.integer-field');
    }
}
