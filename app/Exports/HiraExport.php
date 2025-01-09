<?php

namespace App\Exports;

use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HiraExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        return 'Activity';
    }

    public function headings(): array
    {
        return [
            'Plant',
            'Department',
            'Division',
            'Unit',
            'Section',
            'Year',
            'Activity Name',
            'Creator Employee ID',
            'Creator Name',
            'Sub Activity Name',
            'Start Date',
            'Hazard Date',
            'Gross Likelihood',
            'Gross Impact',
            'Gross Ranking',
            'Existing Control',
            'Mitigation Measures',
            'Residual Likelihood',
            'Residual Impact',
            'Residual Ranking',
            'Further Action Required',
            'Completion Date',
            'Routine Activity',
            'Workers Involved',
            ];
    }
    public function styles(Worksheet $sheet)
    {
        // Apply bold styling to the header row (Row 1)
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        // Optionally auto-size columns
        foreach (range('A', 'X') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }


}

