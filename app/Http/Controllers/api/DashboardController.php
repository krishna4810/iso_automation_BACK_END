<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Eai;
use App\Models\Hira;
use Illuminate\Http\Request;

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
        $countData = $this->getCount($year, $department, $plant);
//        $countGraphData = $this->getGraphData();
        return response() -> json (
            $countData,
//            $countGraphData
        );
    }
    public function getCount($year, $department, $plant) {
        $hiraCount = Hira::where('year', $year)
            ->where('department', $department)
            ->where('plant', $plant)
            ->count();
        $eaiCount = Eai::where('year', $year)
            ->where('department', $department)
            ->where('plant', $plant)
            ->count();
//        $arrCount = Hiras::where('year', $year)
//            ->where('department', $department)
//            ->where('plant', $plant)
//            ->count();
        return [
            'hiraCount' => $hiraCount,
            'eaiCount' => $eaiCount
        ];
    }
//    public function getGraphData() {
//        return response()->json();
//    }

}
