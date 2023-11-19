<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Arr;
use App\Models\Eai;
use App\Models\Hira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getFilterParam()
    {
        $uniqueDepartments = collect([
            Eai::select('department')->distinct()->get()->pluck('department')->toArray(),
            Hira::select('department')->distinct()->get()->pluck('department')->toArray(),
            Arr::select('department')->distinct()->get()->pluck('department')->toArray()
        ])->flatten()->unique()->values();

        $uniqueYears = collect([
            Eai::select('year')->distinct()->get()->pluck('year')->toArray(),
            Hira::select('year')->distinct()->get()->pluck('year')->toArray(),
            Arr::select('year')->distinct()->get()->pluck('year')->toArray()
        ])->flatten()->unique()->values();

        $uniquePlants = collect([
            Eai::select('plant')->distinct()->get()->pluck('plant')->toArray(),
            Hira::select('plant')->distinct()->get()->pluck('plant')->toArray(),
            Arr::select('plant')->distinct()->get()->pluck('plant')->toArray()
        ])->flatten()->unique()->values();

        return response()->json([
            'department' => $uniqueDepartments,
            'year' => $uniqueYears,
            'plant' => $uniquePlants,
        ]);
    }

    public function filterDashboard(Request $request)
    {
        $year = $request->input('year');
        $plant = $request->input('plant');
        $department = $request->input('department');

        $hiraQuery = DB::table('hiras');
        $eaiQuery = DB::table('eais');
        $arrQuery = DB::table('arrs');

        if ($year !== "null") {
            $hiraQuery->where('year', $year);
            $eaiQuery->where('year', $year);
            $arrQuery->where('year', $year);
        }
        if ($department !== "null") {
            $hiraQuery->where('department', $department);
            $eaiQuery->where('department', $department);
            $arrQuery->where('department', $department);
        }
        if ($plant !== "null") {
            $hiraQuery->where('plant', $plant);
            $eaiQuery->where('plant', $plant);
            $arrQuery->where('plant', $plant);
        }
        $hira = $hiraQuery->get();
        $eai = $eaiQuery->get();
        $arr = $arrQuery->get();

        $countData = $this->getCount($hira, $eai, $arr);
        $graphData = $this->getGraphData($hira, $eai, $arr);
        $functionData = $this->getData($hira, $eai, $year, $plant, $department);
        $responseData = array_merge($countData, $graphData, $functionData);

        return response()->json($responseData, 200);
    }

    public function getCount($hira, $eai, $arr)
    {
        $hiraCount = $hira->count();
        $eaiCount = $eai->count();
        $arrIds = $arr->pluck('id');

        $arrRisk = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->get();

        $eaiHighCount = $eai->where('residual_ranking_value', '>=', 6)
            ->where('residual_ranking_value', '<=', 9)
            ->count();

        $hiraHighCount = $hira->where('residual_ranking_value', '>=', 6)
            ->where('residual_ranking_value', '<=', 9)
            ->count();

        $arrHighRiskCount = $arrRisk->where('residual_ranking_value', '>=', 6)
            ->where('residual_ranking_value', '<=', 9)
            ->count();

        $totalCount = $eaiHighCount + $hiraHighCount + $arrHighRiskCount;

        return [
            'hiraCount' => $hiraCount,
            'eaiCount' => $eaiCount,
            'arrCount' => $arrRisk->count(),
            'totalHighResidualCount' => $totalCount
        ];
    }

    public function getGraphData($hiras, $eais, $arr)
    {

        $arrIds = $arr->pluck('id');
        $label = $hiras->pluck('id')->toArray();
        $grossRisks = array_map('intval', $hiras->pluck('gross_ranking_value')->toArray());
        $residualRisks = array_map('intval', $hiras->pluck('residual_ranking_value')->toArray());

        $eai_label = $eais->pluck('id')->toArray();
        $eai_grossRisks = array_map('intval', $eais->pluck('gross_ranking_value')->toArray());
        $eai_residualRisks = array_map('intval', $eais->pluck('residual_ranking_value')->toArray());


        $arr_grossRisks = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->groupBy('asset_id')
            ->pluck(DB::raw('MAX(gross_ranking_value)'))
            ->toArray();

        $arr_residualRisks = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->groupBy('asset_id')
            ->pluck(DB::raw('MAX(residual_ranking_value)'))
            ->toArray();

        $arr_label = $arr->pluck('id')->toArray();


        $hiraGraphData = (object)([
            'labels' => $label,
            'gross_risks' => $grossRisks,
            'residual_risks' => $residualRisks
        ]);

        $eaiGraphData = (object)([
            'labels' => $eai_label,
            'gross_risks' => $eai_grossRisks,
            'residual_risks' => $eai_residualRisks
        ]);

        $arrGraphData = (object)([
            'labels' => $arr_label,
            'gross_risks' => array_map('intval', $arr_grossRisks),
            'residual_risks' => array_map('intval', $arr_residualRisks)
        ]);

        return [
            'hira_graph_data' => $hiraGraphData,
            'eai_graph_data' => $eaiGraphData,
            'arr_graph_data' => $arrGraphData
        ];
    }

    public function getData($hiras, $eai, $year, $plant, $department)
    {
        $arrRisks = DB::table('arrs')
            ->join('arr_risks', 'arrs.id', '=', 'arr_risks.asset_id')
            ->select('arr_risks.*')
            ->where(function ($query) {
                $query->orWhere(function ($subQuery) {
                    $subQuery->where('arr_risks.gross_ranking_value', '=', DB::table('arr_risks')->max('gross_ranking_value'));
                })->orWhere(function ($subQuery) {
                    $subQuery->where('arr_risks.residual_ranking_value', '=', DB::table('arr_risks')->max('residual_ranking_value'));
                });
            });;

        if ($year !== "null") {
            $arrRisks->where('year', $year);
        }
        if ($department !== "null") {
            $arrRisks->where('department', $department);
        }
        if ($plant !== "null") {
            $arrRisks->where('plant', $plant);
        }

        $functionalData = (object)([
            'hira' => $hiras,
            'eai' => $eai,
            'arr' => $arrRisks->get()
        ]);
        return [
            'functional_data' => $functionalData,
        ];
    }

}
