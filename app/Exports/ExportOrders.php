<?php

namespace App\Exports;

use App\Models\Program;
use App\Models\UserInvestment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportOrders implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    
    protected $items;
    protected $date;

    public function __construct($items, $date)
    {
        $this->items = $items;
        $this->date = $date;
    }

    
    public function view(): View
    {
        
        return view('exports.orders', [
            'items' => $this->items,
            'date' => $this->date,
        ]);
    }
}
