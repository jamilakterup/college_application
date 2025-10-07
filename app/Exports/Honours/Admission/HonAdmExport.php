<?php

namespace App\Exports\Honours\Admission;

use App\Exports\Honours\Admission\Sheet\DeptReportExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HonAdmExport implements FromArray, WithMultipleSheets
{
    use Exportable;
    protected $sheets;

    public function __construct($request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [
            new DeptReportExport($this->request, $this->data)
        ];
        return $sheets;
    }


}
