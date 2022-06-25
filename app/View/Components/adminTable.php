<?php

namespace App\View\Components;

use Illuminate\View\Component;

class adminTable extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $items;
    public $headers;
    public $constraints;
    public $controller;
    public $many;

    public function __construct($controller, $items, $headers, $constraints, $many)
    {
        $this->controller = $controller;
        $this->items = $items;
        $this->headers = $headers;
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
        return view('components.admin-table');
    }
}
