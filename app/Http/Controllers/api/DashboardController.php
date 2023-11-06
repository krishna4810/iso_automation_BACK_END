<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
//            Arr::select('department')->distinct()->get()->pluck('department')->toArray()
        ])->flatten()->unique()->values();

        $uniqueYears = collect([
            Eai::select('year')->distinct()->get()->pluck('year')->toArray(),
            Hira::select('year')->distinct()->get()->pluck('year')->toArray(),
//            Arr::select('year')->distinct()->get()->pluck('year')->toArray()
        ])->flatten()->unique()->values();

        $uniquePlants = collect([
            Eai::select('plant')->distinct()->get()->pluck('plant')->toArray(),
            Hira::select('plant')->distinct()->get()->pluck('plant')->toArray(),
//            Arr::select('plant')->distinct()->get()->pluck('plant')->toArray()
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

        $hira = DB::table('hiras')->where('year', $year)
            ->where('department', $department)
            ->where('plant', $plant)
            ->whereIn('status', ['Pre-Activity Details Accepted', 'Post-Activity Details Accepted'])
            ->get();

        $eai = DB::table('eais')->where('year', $year)
            ->where('department', $department)
            ->where('plant', $plant)
            ->whereIn('status', ['Pre-Activity Details Accepted', 'Post-Activity Details Accepted'])
            ->get();
        $countData = $this->getCount($hira, $eai);
        $countGraphData = $this->getGraphData($hira, $eai);
        $responseData = array_merge($countData, $countGraphData);
        return response()->json($responseData, 200);
    }

    public function getCount($hira, $eai)
    {
        $hiraCount = $hira->count();
        $eaiCount = $eai->count();
//        $arrCount = Arr::where('year', $year)
//            ->where('department', $department)
//            ->where('plant', $plant)
//            ->count();
        $eaiHighCount = Eai::where('residual_ranking_value', '>=', 6)
            ->where('residual_ranking_value', '<=', 9)
            ->count();

        $hiraHighCount = Hira::where('residual_ranking_value', '>=', 6)
            ->where('residual_ranking_value', '<=', 9)
            ->count();

        $totalCount = $eaiHighCount + $hiraHighCount;
        return [
            'hiraCount' => $hiraCount,
            'eaiCount' => $eaiCount,
            'totalHighResidualCount' => $totalCount
        ];
    }

    public function getGraphData($hiras, $eais)
    {

        $label = $hiras->pluck('id')->toArray();
        $grossRisks = array_map('intval', $hiras->pluck('gross_ranking_value')->toArray());
        $residualRisks = array_map('intval', $hiras->pluck('residual_ranking_value')->toArray());
        $hiraGraphData = (object)([
            'labels' => $label,
            'gross_risks' => $grossRisks,
            'residual_risks' => $residualRisks
        ]);

        return [
            'hira_graph_data' => $hiraGraphData,
        ];
    }

}
