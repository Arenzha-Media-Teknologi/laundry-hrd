<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmployeeDetailCard extends Component
{

    /**
     * The alert message.
     *
     * @var Employee
     */
    public $employee;

    // public $career;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($employee)
    {
        $this->employee = $employee;
        // $this->career = $career;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.employee-detail-card');
    }
}
