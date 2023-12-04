<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Arr;
use App\Models\ArrRisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArrController extends Controller
{
    public function getDocumentNumber(Request $request)
    {
        $department = $request->input('department');
        $plant = $request->input('plant');
        $unit = $request->input('unit');
        $year = $request->input('year');
        $values = DB::table('arrs')
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


    public function addAssetDetails(Request $request)
    {
        $Arr = Arr::where('doc_number', $request->docNumber)->first();
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
            'asset_name' => $request->input('assetName'),
            'asset_number' => $request->input('assetNumber'),
            'installation_date' => $request->input('installation_date'),
            'make' => $request->input('make'),
        ];
        Arr::updateOrCreate(
            ['doc_number' => $request->input('docNumber')],
            $data
        );
        if ($Arr) {
            return response()->json(['message' => "Asset Details Updated Successfully",]);
        } else {
            return response()->json(['message' => "Asset Details added Successfully, Please Add Risk Statements"]);
        }
    }

    public function getArrRisks(Request $request)
    {

        $role_id = $request->input('role_id');
        $status = $request->input('status');
        $user_id = $request->input('user_id');
        $department = $request->input('department');
        $plant = $request->input('plant');
        $unit = $request->input('division');
        $arrsWithRisks = DB::table('arr_risks')
            ->rightJoin('arrs', 'arrs.id', '=', 'arr_risks.asset_id')
            ->orderBy('arrs.id', 'desc')
            ->when($role_id == 3, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            })
            ->when($role_id == 4 || $role_id == 5, function ($query) use ($plant, $department, $status) {
                return $query->where(function ($query) use ($plant, $department, $status) {
                    if ($plant == 'Corporate Office') {
                        $query->where('department', $department)
                            ->where('status', $status)
                            ->where('plant', $plant);

                    } else {
                        $query->where('plant', $plant)->where('status', $status);
                    }
                });
            })
            ->when($role_id == 5, function ($query) use ($status, $plant, $department, $unit) {
                return $query->where('status', $status)
                    ->where('plant', $plant)
                    ->where('department', $department)
                    ->where('unit', $unit);
            })
            ->when($role_id == 6, function ($query) use ($status, $plant, $department) {
                return $query->where('status', $status)
                    ->where('plant', $plant)
                    ->where('department', $department);
            })
            ->when($role_id == 7, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get()
            ->groupBy('id')
            ->map(function ($item) {
                return [
                    'id' => $item[0]->id,
                    'creator_name' => $item[0]->creator_name,
                    'user_id' => $item[0]->user_id,
                    'doc_number' => $item[0]->doc_number,
                    'department' => $item[0]->department,
                    'plant' => $item[0]->plant,
                    'unit' => $item[0]->unit,
                    'address' => $item[0]->address,
                    'year' => $item[0]->year,
                    'asset_name' => $item[0]->asset_name,
                    'asset_number' => $item[0]->asset_number,
                    'installation_date' => $item[0]->installation_date,
                    'make' => $item[0]->make,
                    'date' => $item[0]->date,
                    'risks' => $item->map(function ($row) {
                        return collect($row)->except(['id', 'date', 'creator_name', 'user_id', 'doc_number', 'department', 'plant', 'unit', 'address', 'year', 'asset_name', 'asset_number', 'installation_date', 'make'])->toArray();
                    })->values()->toArray(),
                ];
            })->values();
        return response()->json($arrsWithRisks);
    }

    public function addRiskDetails(Request $request)
    {
        $Arr = ArrRisk::where('risk_id', $request->input('risk_id'))->first();
        $data = [
            'risk_statement' => $request->input('riskStatement'),
            'asset_id' => $request->input('asset_id'),
            'gross_likelihood' => $request->input('grossLikelihood'),
            'gross_impact' => $request->input('grossImpact'),
            'gross_ranking' => $request->input('grossRanking'),
            'gross_ranking_value' => $request->input('grossRankingValue'),
            'existing_control' => $request->input('existingControl'),
            'further_action_required' => $request->input('furtherAction'),
            'residual_likelihood' => $request->input('residualLikelihood'),
            'residual_impact' => $request->input('residualImpact'),
            'residual_ranking_value' => $request->input('residualRankingValue'),
            'residual_ranking' => $request->input('residualRanking'),
            'status' => $request->input('status'),
        ];
        ArrRisk::updateOrCreate(
            ['risk_id' => $request->input('risk_id')],
            $data
        );
        if ($Arr) {
            return response()->json(['message' => "Risk Details Updated Successfully",]);
        } else {
            return response()->json(['message' => "Risk Details added Successfully"]);
        }
    }


    public function getSpecificFunction(Request $request)
    {
        $id = $request->input('id');

        if (strpos($id, 'E') === 0) {
            $model = DB::table('eais')->where('eais.id', '=', $id)->get();
        } elseif (strpos($id, 'H') === 0) {
            $model = DB::table('hiras')->where('hiras.id', '=', $id)->get();
        } else {
            $model = DB::table('arr_risks')
                ->rightjoin('arrs', 'arrs.id', '=', 'arr_risks.asset_id')
                ->orderBy('arrs.id', 'desc')
                ->where('arrs.id', '=', $id)
                ->get()
                ->groupBy('id')
                ->map(function ($item) {
                    return [
                        'id' => $item[0]->id,
                        'creator_name' => $item[0]->creator_name,
                        'user_id' => $item[0]->user_id,
                        'doc_number' => $item[0]->doc_number,
                        'department' => $item[0]->department,
                        'plant' => $item[0]->plant,
                        'unit' => $item[0]->unit,
                        'address' => $item[0]->address,
                        'year' => $item[0]->year,
                        'asset_name' => $item[0]->asset_name,
                        'asset_number' => $item[0]->asset_number,
                        'installation_date' => $item[0]->installation_date,
                        'make' => $item[0]->make,
                        'date' => $item[0]->date,
                        'risks' => $item->map(function ($row) {
                            return collect($row)->except(['id', 'creator_name', 'user_id', 'doc_number', 'department', 'plant', 'unit', 'address', 'year', 'asset_name', 'asset_number', 'installation_date', 'make'])->toArray();
                        })->values()->toArray(),
                    ];
                })->values();
        }
        return response()->json($model->toArray());
    }

}
