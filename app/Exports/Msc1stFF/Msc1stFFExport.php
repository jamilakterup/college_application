<?php

namespace App\Exports\Msc1stFF;

use App\Exports\Msc1stFF\Sheet\ConsulatedReportExport;
use App\Exports\Msc1stFF\Sheet\DeptReportExport;
use App\Exports\Msc1stFF\Sheet\IndivisualReportExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Msc1stFFExport implements FromArray, WithMultipleSheets
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
        if($this->request->dept_name == ''){
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
