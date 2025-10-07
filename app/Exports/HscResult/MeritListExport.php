<?php

namespace App\Exports\HscResult;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

class MeritListExport implements WithTitle, FromView
{
    use Exportable;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $id = $this->id;
        return view('BackEnd.hsc_result.export.merit_list_excel',compact('id'));
    }

    public function title(): string
    {
        return 'Merit List Sheet';
    }
}
