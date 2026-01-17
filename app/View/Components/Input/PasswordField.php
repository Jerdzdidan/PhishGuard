<?php

namespace App\View\Components\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PasswordField extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $label, $icon, $name, $placeholder, $help;

    public function __construct($id, $label, $icon, $name, $placeholder, $help)
    {
        //
        $this->id = $id;
        $this->label = $label;
        $this->icon = $icon;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->help = $help;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input.password-field');
    }
}
