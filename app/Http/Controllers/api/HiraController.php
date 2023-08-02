<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Hira;
use App\Models\HiraForm;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HiraController extends Controller
{


    public function getDocumentNumber(Request $request)
    {
        $department = $request->input('department');
        $plant = $request->input('plant');
        $unit = $request->input('unit');
        $year = $request->input('year');
        $values = DB::table('hiras')
            ->where('department', $department)
            ->where('unit', $unit)
            ->where('year', $year)
            ->where('plant', $plant)
            ->get();
        $counter = count($values);
        $split_department = implode('', array_map('substr', explode(' ', $department), array_fill(0, count(explode(' ', $department)), 0), array_fill(0, count(explode(' ', $department)), 1)));
        $split_plant = implode('', array_map('substr', explode(' ', $plant), array_fill(0, count(explode(' ', $plant)), 0), array_fill(0, count(explode(' ', $plant)), 1)));
        $split_unit= implode('', array_map('substr', explode(' ', $unit), array_fill(0, count(explode(' ', $unit)), 0), array_fill(0, count(explode(' ', $unit)), 1)));

        $doc_number = 'DGPC/' . $split_plant.'/'.$split_department.'/'.$split_unit.'/'.$year.'/'.$counter+1;

        return response()->json([
            'documentNumber' => $doc_number,
        ]);
    }

    public function addHira(Request $request) {

        $data = [
            'department' => $request->input('department'),
            'doc_number' => $request->input('docNumber'),
            'date' => $request->input('date'),
            'year' => $request->input('year'),
            'plant' => $request->input('plant'),
            'unit' => $request->input('unit'),
            'address' => $request->input('address'),
            'activity_name' => $request->input('activityName'),
            'sub_activity_name' => $request->input('subActivityName'),
            'hazard' => $request->input('hazard'),
            'start_date' => $request->input('start_date'),
            'gross_likelihood' => $request->input('g_likelihood'),
            'gross_impact' => $request->input('g_impact'),
            'gross_ranking' => $request->input('g_ranking'),
        ];
        Hira::create($data);
        return response()->json(['message' => "Hira Function added Successfully"]);

    }

    public function getHira() {
        $hiras = DB::table('hiras')
            ->orderBy('id','DESC')
            ->get();
        if ($hiras->isEmpty()) {
            return response()->json(['message' => 'Failed to load Data'], 400);
        } else {
            return response()->json($hiras);
        }
    }

    public function getHiraForms() {
        $form = DB::table('hira_forms')
            ->select('hira_forms.id', 'hira_forms.name', 'hira_forms.category', 'hira_forms.column_value')
            ->get();
        return response()->json($form);
    }

    public function addNewField(Request $request)
    {
        $tableName = 'hiras';
        $columnName = $request->input('column_value');
        $columnType = 'string';
        $isNullable = true;

        // Check if the column already exists in the table
        if (!Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName, $columnType, $isNullable) {
                $column = $table->$columnType($columnName);
                if ($isNullable) {
                    $column->nullable();
                }
            });
            $newForm  = new HiraForm();
            $newForm->name = $request->input('name');
            $newForm->column_value = $request->input('column_value');
            $newForm->category = $request->input('category');
            $newForm->save();
            return response()->json(['message' => 'Field added successfully.'], 200);
        }

        return response()->json(['message' => 'The ' . $columnName . ' already exists in the table.'], 400);
    }

    public function deleteField(Request $request)
    {
        $columnName = $request->input('column');
        $form_id = $request->input('id');
        $tableName = 'hiras';

        // Check if the column exists in the table
        if (Schema::hasColumn($tableName, $columnName)) {
            // Use a migration to remove the column from the table
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
            DB::table('hira_forms')->where('id', $form_id)->delete();
            return response()->json(['message' => 'Field deleted successfully.'], 200);
        }
        return response()->json(['message' => 'The '. $columnName. ' does not exist in the table.'], 400);
    }


}
