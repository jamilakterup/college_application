<?php

namespace App\Exports\HscResult;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

class TabulationExport implements WithTitle, FromView
{
    use Exportable;

    public function __construct($id, $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function view(): View
    {
        $id = $this->id;
        $data = $this->data;
        return view('BackEnd.hsc_result.export.tabulation_excel',compact('id', 'data'));
    }

    public function title(): string
    {
        return 'Tabulation Sheet';
    }
}
