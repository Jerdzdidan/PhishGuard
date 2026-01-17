<?php

namespace App\View\Components\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectField extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $label, $prop, $options;

    public function __construct($id, $label, $prop = '', $options = null)
    {
        //
        $this->id = $id;
        $this->label = $label;
        $this->prop = $prop;
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input.select-field');
    }
}
