<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Eai;
use App\Models\Hira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EaiController extends Controller
{
    public function getDocumentNumber(Request $request)
    {
        $department = $request->input('department');
        $plant = $request->input('plant');
        $unit = $request->input('unit');
        $year = $request->input('year');
        $values = DB::table('eais')
            ->where('department', $department)
            ->where('unit', $unit)
            ->where('year', $year)
            ->where('plant', $plant)
            ->get();
        $counter = count($values);
        $split_department = implode('', array_map('substr', explode(' ', $department), array_fill(0, count(explode(' ', $department)), 0), array_fill(0, count(explode(' ', $department)), 1)));
        $split_plant = implode('', array_map('substr', explode(' ', $plant), array_fill(0, count(explode(' ', $plant)), 0), array_fill(0, count(explode(' ', $plant)), 1)));
        $split_unit = implode('', array_map('substr', explode(' ', $unit), array_fill(0, count(explode(' ', $unit)), 0), array_fill(0, count(explode(' ', $unit)), 1)));

        $doc_number = 'DGPC/' . $split_plant . '/' . $split_department . '/' . $split_unit . '/' . $year . '/' . $counter + 1;

        return response()->json([
            'documentNumber' => $doc_number,
        ]);
    }

    public function addEai(Request $request)
    {
        $Eai = Eai::where('doc_number', $request->docNumber)->first();
        $data = [
            'department' => $request->input('department'),
            'doc_number' => $request->input('docNumber'),
            'user_id' => $request->input('userID'),
            'creator_name' => $request->input('creatorName'),
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
            'gross_ranking_value' => $request->input('grossRankingValue'),
            'existing_control' => $request->input('existingControl'),
            'completion_date' => $request->input('completion_date'),
            'mitigation_measures' => $request->input('mitigationMeasures'),
            'further_action_required' => $request->input('furtherAction'),
            'routine_activity' => $request->input('routineActivity'),
            'workers_involved' => $request->input('workersInvolved'),
            'residual_likelihood' => $request->input('r_likelihood'),
            'residual_impact' => $request->input('r_impact'),
            'residual_ranking_value' => $request->input('residualRankingValue'),
            'residual_ranking' => $request->input('r_ranking'),
            'status' => $request->input('status'),
        ];
        Eai::updateOrCreate(
            ['doc_number' => $request->input('docNumber')],
            $data
        );
        if ($Eai) {
            return response()->json(['message' => "EAI Function Updated Successfully"]);
        } else {
            return response()->json(['message' => "EAI Function added Successfully"]);
        }
    }

    public function getEai(Request $request)
    {
        $role_id = $request->input('role_id');
        $status = $request->input('status');
        $user_id = $request->input('user_id');
        $department = $request->input('department');
        $plant = $request->input('plant');
        $unit = $request->input('division');
        $query = DB::table('eais')->orderBy('id', 'DESC');
        if ($role_id == 3) {
            $query->where('user_id', $user_id);
        } elseif ($role_id == 4 || $role_id == 5) {
            if ($plant == 'Corporate Office') {
                $query->where('plant', $plant)
                    ->where('department', $department)
                    ->where('status', $status);
            } else {
                $query->where('plant', $plant)
                    ->where('status', $status);
            }
        } elseif ($role_id == 6) {
            $query->where('status', $status)
                ->where('plant', $plant)
                ->where('department', $department);
        } elseif ($role_id == 7) {
            $query->where('status', $status);
        }
        $eais = $query->get();
        return response()->json($eais);
    }


}
