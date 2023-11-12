<?php

namespace hcolab\cms\exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GridExport implements FromCollection , WithHeadings
{

    public $rows;
    public $columns;

    public function __construct($rows , $columns)
    {
        $this->rows = $rows;
        $this->columns = $columns;
    }

    public function headings(): array
    {
        return $this->columns;
    }

    public function collection()
    {
        return $this->rows;
        // dd("here");
        // return Invoice::all();
    }
}