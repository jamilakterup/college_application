<?php

namespace App\Exports\HSCFF;

use App\Exports\HSCFF\Sheet\ConsulatedReportExport;
use App\Exports\HSCFF\Sheet\DeptReportExport;
use App\Exports\HSCFF\Sheet\IndivisualReportExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HSCFFExport implements FromArray, WithMultipleSheets
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
        if($this->request->groups == ''){
            $sheets = [
                new IndivisualReportExport($this->request, $this->data),
                new ConsulatedReportExport($this->request, $this->data),
            ];
        }else{
            $sheets = [
                new DeptReportExport($this->request, $this->data)
            ];
        }

        return $sheets;
    }


}
