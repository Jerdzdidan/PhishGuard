<?php

namespace App\View\Components\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputField extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $label, $icon, $type, $name, $placeholder, $help;
    
    public function __construct($id, $label, $name, string $type = 'text',
    string $placeholder = '',
    string $help = '',
    string $icon = '')
    {
        //
        $this->id = $id;
        $this->label = $label;
        $this->icon = $icon;
        $this->type = $type;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->help = $help;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input.input-field');
    }
}
