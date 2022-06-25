<?php

namespace App\View\Components;

use Illuminate\View\Component;

class itemEditorAdmin extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $headers;
    public $constraints;
    public $controller;
    public $many;
    public $prev;

    public function __construct($controller, $headers, $constraints, $many, $prev)
    {
        $this->controller = $controller;
        $this->headers = $headers;
        $this->prev = $prev;
        if($constraints!='null') {
            $this->constraints = $constraints;
        }
        if($many!='null') {
            $this->many = $many;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.item-editor-admin');
    }
}
