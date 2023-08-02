<?php

namespace Database\Seeders;

use App\Models\HiraForm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HiraFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HiraForm::create([
            'name' => 'Document Number',
            'column_value' => 'doc_number',
            'category' => 1
        ]);
        HiraForm::create([
            'name' => 'Department',
            'column_value' => 'department',
            'category' => 1
        ]);
        HiraForm::create([
            'name' => 'Date',
            'column_value' => 'date',
            'category' => 1
        ]);
        HiraForm::create([
            'name' => 'Plant',
            'column_value' => 'plant',
            'category' => 1
        ]);
        HiraForm::create([
            'name' => 'Division/Unit',
            'column_value' => 'division',
            'category' => 1
        ]);
        HiraForm::create([
            'name' => 'Address',
            'column_value' => 'address',
            'category' => 1
        ]);

        HiraForm::create([
            'name' => 'Activity Name',
            'column_value' => 'activity_name',
            'category' => 2
        ]);

        HiraForm::create([
            'name' => 'Sub Activity Name',
            'column_value' => 'sub_activity_name',
            'category' => 2
        ]);

        HiraForm::create([
            'name' => 'Start Date',
            'column_value' => 'start_date',
            'category' => 2
        ]);

        HiraForm::create([
            'name' => 'Gross Likelihood',
            'column_value' => 'gross_likelihood',
            'category' => 2
        ]);

        HiraForm::create([
            'name' => 'Gross Impact',
            'column_value' => 'gross_impact',
            'category' => 2
        ]);
        HiraForm::create([
            'name' => 'Gross Ranking',
            'column_value' => 'gross_ranking',
            'category' => 2
        ]);

        HiraForm::create([
            'name' => 'Existing Control',
            'column_value' => 'existing_control',
            'category' => 3
        ]);

        HiraForm::create([
            'name' => 'Hazard',
            'column_value' => 'hazard',
            'category' => 2
        ]);

        HiraForm::create([
            'name' => 'Completion Date',
            'column_value' => 'completion_date',
            'category' => 3
        ]);

        HiraForm::create([
            'name' => 'Routine/Non Routine Activity',
            'column_value' => 'routine_activity',
            'category' => 3
        ]);

        HiraForm::create([
            'name' => 'Workers Involved',
            'column_value' => 'workers_involved',
            'category' => 3
        ]);

        HiraForm::create([
            'name' => 'Residual Likelihood',
            'column_value' => 'residual_likelihood',
            'category' => 3
        ]);

        HiraForm::create([
            'name' => 'Residual Impact',
            'column_value' => 'residual_impact',
            'category' => 3
        ]);

        HiraForm::create([
            'name' => 'Residual Ranking',
            'column_value' => 'residual_ranking',
            'category' => 3
        ]);
    }
}
