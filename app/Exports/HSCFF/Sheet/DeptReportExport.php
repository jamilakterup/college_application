<?php

namespace App\Exports\HSCff\Sheet;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DeptReportExport implements WithTitle, FromView
{
    private $request;
    private $data;

    public function __construct($request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function view(): View
    {
        $data = $this->data;
        $request = $this->request;
        return view('BackEnd.student.report.excel.hscff.department',compact('data', 'request'));
    }

    public function title(): string
    {
        return 'Department Report';
    }
}
