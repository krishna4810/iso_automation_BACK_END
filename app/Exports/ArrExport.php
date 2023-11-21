<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ArrExport implements FromCollection, WithTitle, WithHeadings
{
    use Exportable;

    protected $filteredData;

    public function __construct(Collection $filteredData)
    {
        $this->filteredData = $filteredData;
    }

    public function collection()
    {
        return $this->filteredData;
    }

    public function title(): string
    {
        return 'sheet 1';
    }

    public function headings(): array
    {
        return [
            'Asset ID',
            'Document Number',
            'Creator Name',
            'Date',
            'Plant',
            'Department',
            'Unit',
            'Asset Name',
            'Asset Number',
            'Installation Date',
            'Make',
            'Risk Statement',
            'Gross Likelihood',
            'Gross Impact',
            'Gross Ranking',
            'Existing Control',
            'Further Action Required',
            'Residual Likelihood',
            'Residual Impact',
            'Residual Ranking'];
    }

}

