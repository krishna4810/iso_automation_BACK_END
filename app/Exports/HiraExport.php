<?php

namespace App\Exports;

use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class HiraExport implements FromCollection, WithTitle, WithHeadings
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
        return 'HIRA';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Document Number',
            'Creator Name',
            'Date',
            'Plant',
            'Department',
            'Unit',
            'Activity Name',
            'Sub Activity Name',
            'Hazard',
            'Start Date',
            'Gross Likelihood',
            'Gross Impact',
            'Gross Ranking',
            'Existing Control',
            'Completion Date',
            'Mitigation Measures',
            'Further Action Required',
            'Routine Activity',
            'Workers Involved',
            'Residual Likelihood',
            'Residual Impact',
            'Residual Ranking'];
    }

}

