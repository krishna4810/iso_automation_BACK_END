<?php

namespace App\Http\Controllers\api;

use App\Exports\HiraExport;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Arr;
use App\Models\Eai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function generateReport(Request $request)
    {

        $plant = $request->input('plant');
        $year = $request->input('year');
        $department = $request->input('department');
        $quarter = $request->input('quarter');
        $type = $request->input('type');

        if ($type == 'arr') {
            $query = DB::table('arrs')
                ->rightJoin('arr_risks', 'arrs.id', '=', 'asset_id');

        } else if ($type = 'hira') {
            $hiraTableQuery = DB::table('activities')
                ->select('activities.id', 'activities.activity_name', 'activities.year')
                ->leftJoin('sub_activities', 'activities.id', '=', 'sub_activities.activity_id')
                ->leftJoin('hazards', 'sub_activities.id', '=', 'hazards.sub_activity_id')
                ->select([

                    'activities.plant',
                    'activities.department',
                    'activities.division',
                    'activities.unit',
                    'activities.section',
                    'activities.year',
                    'activities.activity_name',
                    'activities.user_id',
                    'activities.creator_name',
                    'sub_activities.sub_activity_name',
                    'sub_activities.start_date',
                    'hazards.hazard_name',
                    'hazards.gross_likelihood',
                    'hazards.g_impact',
                    'hazards.g_ranking',
                    'hazards.existing_control',
                    'hazards.mitigation_measures',
                    'hazards.residual_likelihood',
                    'hazards.residual_impact',
                    'hazards.residual_ranking',
                    'hazards.further_action_required',
                    'sub_activities.completion_date',
                    'sub_activities.routine_non',
                    'sub_activities.workers_involved',
                ]);
        }
        if ($year !== "null") {
            $hiraTableQuery->where('activities.year', $year);
            if ($department !== "null") {

                $hiraTableQuery->where(fn($query) => $query->where('activities.division', $department)
                    ->orWhere('activities.unit', $department)
                    ->orWhere('activities.section', $department)
                );
            }
            if ($plant !== "null") {
                $hiraTableQuery->where(fn($query) => $query->where('activities.plant', $plant)
                    ->orWhere('activities.department', $plant));
            }
            if ($quarter !== "null") {
                switch ($quarter) {
                    case 1: // January to March
                        $start = now()->startOfYear()->format('Y-01-01');
                        $end = now()->startOfYear()->addMonths(3)->endOfMonth()->format('Y-m-d');
                        break;
                    case 2: // April to June
                        $start = now()->startOfYear()->addMonths(3)->format('Y-04-01');
                        $end = now()->startOfYear()->addMonths(6)->endOfMonth()->format('Y-m-d');
                        break;
                    case 3: // July to September
                        $start = now()->startOfYear()->addMonths(6)->format('Y-07-01');
                        $end = now()->startOfYear()->addMonths(9)->endOfMonth()->format('Y-m-d');
                        break;
                    case 4: // October to December
                        $start = now()->startOfYear()->addMonths(9)->format('Y-10-01');
                        $end = now()->endOfYear()->format('Y-12-31');
                        break;
                }

                $hiraTableQuery->whereBetween('activities.created_at', [$start, $end]);
            }
        }

        return (new HiraExport($hiraTableQuery->get()))->download($type . '_report.xlsx');


    }

    public function getFilterParam()
    {
        $uniqueDepartments = collect([
            Activity::where('unit', '!=', 'NA')->distinct()->get()->pluck('unit')->toArray(),
            Activity::where('section', '!=', 'NA')->distinct()->pluck('section')->toArray(),
            Activity::where('division', '!=', 'NA')->distinct()->get()->pluck('division')->toArray(),
        ])->flatten()->unique()->values();

        $uniqueYears = collect([
            Eai::select('year')->distinct()->get()->pluck('year')->toArray(),
            Activity::select('year')->distinct()->get()->pluck('year')->toArray(),
            Arr::select('year')->distinct()->get()->pluck('year')->toArray()
        ])->flatten()->unique()->values();

        $uniquePlants = collect([
            Activity::select('plant')->distinct()->get()->pluck('plant')->toArray(),
            Activity::select('department')->distinct()->get()->pluck('department')->toArray(),
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
        $quarter = $request->input('quarter');

        // Base Query for HIRA
        $hiraQuery = DB::table('activities')
            ->select('activities.id', 'activities.activity_name', 'activities.year')
            ->leftJoin('sub_activities', 'activities.id', '=', 'sub_activities.activity_id')
            ->leftJoin('hazards', 'sub_activities.id', '=', 'hazards.sub_activity_id')
            ->groupBy('activities.id', 'activities.activity_name', 'activities.year')
            ->selectRaw('activities.id, activities.activity_name, activities.year,
        COALESCE(ROUND(AVG(hazards.g_ranking_value)), 0) as g_ranking_value,
        COALESCE(ROUND(AVG(hazards.residual_ranking_value)), 0) as residual_ranking_value'
            );

        $hiraQueryHeatMap = DB::table('activities')
            ->select('activities.id', 'activities.activity_name', 'activities.year')
            ->leftJoin('sub_activities', 'activities.id', '=', 'sub_activities.activity_id')
            ->leftJoin('hazards', 'sub_activities.id', '=', 'hazards.sub_activity_id')
            ->groupBy('activities.id', 'activities.activity_name', 'activities.year')
            ->selectRaw('activities.id, activities.activity_name, activities.year,
        COALESCE(ROUND(AVG(hazards.gross_likelihood)), 0) as gross_likelihood,
        COALESCE(ROUND(AVG(hazards.residual_likelihood)), 0) as residual_likelihood'
            );

        $hiraTableQuery = DB::table('activities')
            ->select('activities.id', 'activities.activity_name', 'activities.year')
            ->leftJoin('sub_activities', 'activities.id', '=', 'sub_activities.activity_id')
            ->leftJoin('hazards', 'sub_activities.id', '=', 'hazards.sub_activity_id')
            ->select('*');


        $eaiQuery = DB::table('eais');

        $arrQuery = DB::table('arrs');


        if ($year !== "null") {
            $hiraQuery->where('activities.year', $year);
            $hiraQueryHeatMap->where('activities.year', $year);
            $hiraTableQuery->where('activities.year', $year);
            $eaiQuery->where('year', $year);
            $arrQuery->where('year', $year);
        }
        if ($department !== "null") {
            $hiraQuery->where(fn($query) => $query->where('activities.division', $department)
                ->orWhere('activities.unit', $department)
                ->orWhere('activities.section', $department)
            );
            $hiraTableQuery->where(fn($query) => $query->where('activities.division', $department)
                ->orWhere('activities.unit', $department)
                ->orWhere('activities.section', $department)
            );
            $hiraQueryHeatMap->where(fn($query) => $query->where('activities.division', $department)
                ->orWhere('activities.unit', $department)
                ->orWhere('activities.section', $department)
            );
            $eaiQuery->where('unit', $department);
            $arrQuery->where('unit', $department);
        }
        if ($plant !== "null") {
            $hiraQuery->where(fn($query) => $query->where('activities.plant', $plant)
                ->orWhere('activities.department', $plant));
            $hiraQueryHeatMap->where(fn($query) => $query->where('activities.plant', $plant)
                ->orWhere('activities.department', $plant));
            $hiraTableQuery->where(fn($query) => $query->where('activities.plant', $plant)
                ->orWhere('activities.department', $plant));

            $eaiQuery->where('plant', $plant);
            $arrQuery->where('plant', $plant);
        }
        if ($quarter !== "null") {
            switch ($quarter) {
                case 1: // January to March
                    $start = now()->startOfYear()->format('Y-01-01');
                    $end = now()->startOfYear()->addMonths(3)->endOfMonth()->format('Y-m-d');
                    break;
                case 2: // April to June
                    $start = now()->startOfYear()->addMonths(3)->format('Y-04-01');
                    $end = now()->startOfYear()->addMonths(6)->endOfMonth()->format('Y-m-d');
                    break;
                case 3: // July to September
                    $start = now()->startOfYear()->addMonths(6)->format('Y-07-01');
                    $end = now()->startOfYear()->addMonths(9)->endOfMonth()->format('Y-m-d');
                    break;
                case 4: // October to December
                    $start = now()->startOfYear()->addMonths(9)->format('Y-10-01');
                    $end = now()->endOfYear()->format('Y-12-31');
                    break;
            }

            $hiraQuery->whereBetween('activities.created_at', [$start, $end]);
            $hiraTableQuery->whereBetween('activities.created_at', [$start, $end]);
            $hiraQueryHeatMap->whereBetween('activities.created_at', [$start, $end]);
        }

        // Execute the query
        $hira = $hiraQuery->get();
        $hiraHeatMap = $hiraQueryHeatMap->get();
        $hiraDataQuery = $hiraTableQuery->get();
        $eai = $eaiQuery->get();
        $arr = $arrQuery->get();

        $countData = $this->getCount($hira, $eai, $arr);
        $graphData = $this->getGraphData($hira, $eai, $arr);
        $heatMapData = $this->getHeatMapData($hiraHeatMap);
        $functionData = $this->getData($hiraDataQuery);
        $responseData = array_merge($countData, $graphData, $heatMapData, $functionData);

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

    public
    function getGraphData($hiras, $eais, $arr)
    {


        $labels = $hiras->pluck('id')->map(function ($id) {
            return 'H' . $id;
        })->toArray();

        $grossRisks = $hiras->pluck('g_ranking_value')->map(fn($value) => (int)$value)->toArray();
        $residualRisks = $hiras->pluck('residual_ranking_value')->map(fn($value) => (int)$value)->toArray();

        $eai_label = $eais->pluck('id')->toArray();
        $eai_grossRisks = array_map('intval', $eais->pluck('gross_ranking_value')->toArray());
        $eai_residualRisks = array_map('intval', $eais->pluck('residual_ranking_value')->toArray());

        $arrIds = $arr->pluck('id');

        $arr_grossRisks = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->select('asset_id', DB::raw('AVG(gross_ranking_value) as avg_gross_ranking_value'))
            ->groupBy('asset_id')
            ->orderBy('asset_id')
            ->pluck('avg_gross_ranking_value', 'asset_id')
            ->toArray();


        $arr_residualRisks = DB::table('arr_risks')
            ->whereIn('asset_id', $arrIds)
            ->select('asset_id', DB::raw('AVG(residual_ranking_value) as avg_residual_ranking_value'))
            ->groupBy('asset_id')
            ->orderBy('asset_id')
            ->pluck('avg_residual_ranking_value', 'asset_id')
            ->toArray();

        $arr_label = $arr->pluck('id')->toArray();


        $hiraGraphData = (object)([
            'labels' => $labels,
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

    public function getHeatMapData($hiras)
    {
        $hiraHeatMapData = $hiras->map(function ($hira) {
            return [
                'id' => 'H' . $hira->id,
                'gross_likelihood' => (string)$hira->gross_likelihood,
                'gross_impact' => (string)$hira->gross_likelihood,
                'residual_likelihood' => (string)$hira->residual_likelihood,
                'residual_impact' => (string)$hira->residual_likelihood,
            ];
        });

        return [
            'hira_heatMap_data' => $hiraHeatMapData,
        ];


    }

    public
    function getData($hiras)
    {

        $functionalData = (object)([
            'hira' => $hiras,

        ]);
        return [
            'functional_data' => $functionalData,
        ];
    }
}
