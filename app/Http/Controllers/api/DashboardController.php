<?php

namespace App\Http\Controllers\api;

use App\Exports\ArrExport;
use App\Exports\HiraExport;
use App\Http\Controllers\Controller;
use App\Models\Arr;
use App\Models\Eai;
use App\Models\Hira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function generateReport(Request $request)
    {

        $plant = $request->input('plant');
        $year = $request->input('year');
        $department = $request->input('department');
        $type = $request->input('type');

        $query = ($type == 'hira' ? DB::table('hiras') : DB::table('eais'));

        if ($type == 'arr') {
            $query = DB::table('arrs')
                ->rightJoin('arr_risks', 'arrs.id', '=', 'asset_id');
        }
        if ($year !== "null") {
            $query->where('year', $year);
        }
        if ($department !== "null") {
            $query->where('department', $department);
        }
        if ($plant !== "null") {
            $query->where('plant', $plant);
        }
        if ($type == 'arr') {
            $filteredData = $query
                ->select('id',
                    'doc_number',
                    'creator_name',
                    'date',
                    'plant',
                    'department',
                    'unit',
                    'asset_name',
                    'asset_number',
                    'installation_date',
                    'make',
                    'risk_statement',
                    'gross_likelihood',
                    'gross_impact',
                    'gross_ranking',
                    'existing_control',
                    'further_action_required',
                    'residual_likelihood',
                    'residual_impact',
                    'residual_ranking'
                )
                ->get();
            return (new ArrExport($filteredData))->download($type . '_report.xlsx');
        } else {
            $filteredData = $query
                ->select('id',
                    'doc_number',
                    'creator_name',
                    'date',
                    'plant',
                    'department',
                    'unit',
                    'activity_name',
                    'sub_activity_name',
                    'hazard',
                    'start_date',
                    'gross_likelihood',
                    'gross_impact',
                    'gross_ranking',
                    'existing_control',
                    'completion_date',
                    'mitigation_measures',
                    'further_action_required',
                    'routine_activity',
                    'workers_involved',
                    'residual_likelihood',
                    'residual_impact',
                    'residual_ranking')
                ->get();

            return (new HiraExport($filteredData))->download($type . '_report.xlsx');
        }

    }

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
        $functionData = $this->getData($hira, $eai, $arr, $year, $plant, $department);
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


        $label = $hiras->pluck('id')->toArray();
        $grossRisks = array_map('intval', $hiras->pluck('gross_ranking_value')->toArray());
        $residualRisks = array_map('intval', $hiras->pluck('residual_ranking_value')->toArray());

        $eai_label = $eais->pluck('id')->toArray();
        $eai_grossRisks = array_map('intval', $eais->pluck('gross_ranking_value')->toArray());
        $eai_residualRisks = array_map('intval', $eais->pluck('residual_ranking_value')->toArray());

        $arrIds = $arr->pluck('id');
        $arr_grossRisks = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->select('asset_id', DB::raw('AVG(gross_ranking_value) as avg_gross_ranking_value'))
            ->groupBy('asset_id')
            ->orderBy('asset_id') // Add this line to ensure the order of results
            ->pluck('avg_gross_ranking_value', 'asset_id')
            ->toArray();


        $arr_residualRisks = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->select('asset_id', DB::raw('AVG(residual_ranking_value) as avg_residual_ranking_value'))
            ->groupBy('asset_id')
            ->orderBy('asset_id') // Add this line to ensure the order of results
            ->pluck('avg_residual_ranking_value', 'asset_id')
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

    public function getData($hiras, $eai, $arr, $year, $plant, $department)
    {
        $arrRisks = DB::table('arrs')
            ->join('arr_risks', 'arrs.id', '=', 'asset_id')
            ->select('*');
        if ($year !== "null") {
            $arrRisks->where('year', $year);
        }
        if ($department !== "null") {
            $arrRisks->where('department', $department);
        }
        if ($plant !== "null") {
            $arrRisks->where('plant', $plant);
        }

        $arrIds = $arr->pluck('id');

        $arr_residualRisks = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->select('asset_id', DB::raw('MAX(residual_ranking_value) as avg_residual_ranking_value'))
            ->groupBy('asset_id')
            ->orderBy('asset_id') // Add this line to ensure the order of results
            ->pluck('avg_residual_ranking_value', 'asset_id')
            ->toArray();

        $functionalData = (object)([
            'hira' => $hiras,
            'eai' => $eai,
            'arr' => $arrRisks->get(),
            'arrHeatMap' => $arr_residualRisks
        ]);
        return [
            'functional_data' => $functionalData,
        ];
    }
}
