<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\SubActivity;
use App\Models\Hazard;
use App\Models\ActivityLog;
use App\Models\ArrRisk;
use App\Models\Eai;
use App\Models\Hira;
use App\Models\HiraForm;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $split_unit = implode('', array_map('substr', explode(' ', $unit), array_fill(0, count(explode(' ', $unit)), 0), array_fill(0, count(explode(' ', $unit)), 1)));

        $doc_number = 'DGPC/' . $split_plant . '/' . $split_department . '/' . $split_unit . '/' . $year . '/' . $counter + 1;

        return response()->json([
            'documentNumber' => $doc_number,
        ]);
    }

    public function addHira(Request $request)
    {
        $data = $request->all();

        // Create Activity
        $activity = Activity::create($data['activity']);

        // Log the creation
        ActivityLog::create([
            'activity_id' => $activity->id,
            'activity' => "{$data['activity']['creator_name']} created HIRA on " . now()->timezone('GMT+6')->format('D, M d, Y g:i A'),
        ]);

        // Loop through sub-activities
        foreach ($data['sub-activity'] as $subActivityData) {
            $subActivity = $activity->subActivities()->create($subActivityData);

            // Loop through hazards and associate with sub-activity
            foreach ($subActivityData['hazards'] as $hazardData) {
                $subActivity->hazards()->create(array_merge($hazardData, ['activity_id' => $activity->id]));
            }
        }

        return response()->json(['message' => 'HIRA data saved successfully'], 200);
    }

    public function editHira(Request $request)
    {
        $data = $request->all();

        // Fetch the activity by activity_id
        $activity = Activity::find($data['activity']['activity_id']);

        if (!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        // Update the activity fields
        $activity->update($data['activity']);

        // Log the update
        ActivityLog::create([
            'activity_id' => $activity->id,
            'activity' => "{$data['activity']['creator_name']} updated HIRA on " . now()->timezone('GMT+6')->format('D, M d, Y g:i A'),
        ]);

        // Delete sub-activities and associated hazards if IDs are provided
        if (!empty($data['activity']['toBeDeletedSubActivity'])) {
            foreach ($data['activity']['toBeDeletedSubActivity'] as $subActivityId) {
                // Delete hazards for the sub-activity
                Hazard::where('sub_activity_id', $subActivityId)->delete();

                // Delete the sub-activity
                SubActivity::where('id', $subActivityId)->delete();
            }
        }

        // Delete hazards if IDs are provided
        if (!empty($data['toBeDeletedHazard'])) {
            Hazard::whereIn('id', $data['toBeDeletedHazard'])->delete();
        }

        // Process sub-activities
        foreach ($data['sub-activity'] as $subActivityData) {
            // Check if sub-activity exists
            $subActivity = $activity->subActivities()->where('sub_activity_name', $subActivityData['sub_activity_name'])->first();

            if ($subActivity) {
                // Update existing sub-activity
                $subActivity->update($subActivityData);
            } else {
                // Create new sub-activity
                $subActivity = $activity->subActivities()->create($subActivityData);
            }

            // Process hazards
            foreach ($subActivityData['hazards'] as $hazardData) {
                // Check if hazard exists for the sub-activity
                $hazard = $subActivity->hazards()->where('hazard_name', $hazardData['hazard_name'])->first();

                if ($hazard) {
                    // Update existing hazard
                    $hazard->update($hazardData);
                } else {
                    // Create new hazard
                    $subActivity->hazards()->create(array_merge($hazardData, ['activity_id' => $activity->id]));
                }
            }
        }

        return response()->json(['message' => 'HIRA data updated successfully'], 200);
    }


    public function getLogs($activityId)
    {
        // Fetch logs directly from the ActivityLog model where activity_id matches the provided ID
        $logs = ActivityLog::where('activity_id', $activityId)->get();

        // Check if logs are empty
        if ($logs->isEmpty()) {
            return response()->json([
                'message' => 'No logs found for the given activity ID.'
            ], 404); // 404 Not Found status
        }

        return response()->json($logs, 200); // 200 OK status
    }

    public function getActivities(Request $request)
    {
        $role_id = $request->input('role_id');
        $status = $request->input('status');
        $user_id = $request->input('user_id');
        $department = $request->input('department');
        $plant = $request->input('plant');
        $unit = $request->input('division'); // Unused variable in this query

        // Initialize the base query
        $query = DB::table('activities')->orderBy('id', 'DESC');

        // Apply conditions based on the role
        switch ($role_id) {
            case 3:
                $query->where('user_id', $user_id);
                break;

            case 4:
            case 5:
                $query->where('plant', $plant)
                    ->when($plant === 'Corporate Office', function ($query) use ($department, $status) {
                        return $query->where('department', $department)
                            ->where('status', $status);
                    }, function ($query) use ($status) {
                        return $query->where('status', $status);
                    });
                break;

            case 6:
                $query->where('plant', $plant)
                    ->where('department', $department)
                    ->where('status', $status);
                break;

            case 7:
                $query->where('status', $status);
                break;
        }

        // Execute the query
        $activity = $query->get();

        // Return the response
        return response()->json($activity);
    }


//    public function getHira(Request $request)
//    {
//        $role_id = $request->input('role_id');
//        $status = $request->input('status');
//        $user_id = $request->input('user_id');
//        $department = $request->input('department');
//        $plant = $request->input('plant');
//        $unit = $request->input('division');
//        $query = DB::table('hiras')->orderBy('id', 'DESC');
//        if ($role_id == 3) {
//            $query->where('user_id', $user_id);
//        } elseif ($role_id == 4 || $role_id == 5) {
//            if ($plant == 'Corporate Office') {
//                $query->where('plant', $plant)
//                    ->where('department', $department)
//                    ->where('status', $status);
//            } else {
//                $query->where('plant', $plant)
//                    ->where('status', $status);
//            }
//        } elseif ($role_id == 6) {
//            $query->where('status', $status)
//                ->where('plant', $plant)
//                ->where('department', $department);
//        } elseif ($role_id == 7) {
//            $query->where('status', $status);
//        }
//        $hiras = $query->get();
//        return response()->json($hiras);
//    }


    public function getSubActivities($activity_id)
    {
        // Fetch the activity details
        $activity = Activity::find($activity_id);

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        // Fetch sub-activities and their hazards
        $subActivities = SubActivity::where('activity_id', $activity_id)
            ->with('hazards')
            ->get();

        // Fetch logs directly from the ActivityLog model where activity_id matches the provided ID
        $logs = ActivityLog::where('activity_id', $activity_id)
            ->orderBy('created_at', 'desc') // Sort by created_at in descending order
            ->get();


        // Check if logs are empty
        if ($logs->isEmpty()) {
            return response()->json([
                'message' => 'No logs found for the given activity ID.'
            ], 404); // 404 Not Found status
        }

        // Structure the response
        $response = [
            'activity' => [
                'activity_id' => $activity->id, // Include activity_id,
                'activity_name' => $activity->activity_name,
                'functional_type' => 'HIRA',
                'plant' => $activity->plant,
                'department' => $activity->department,
                'division' => $activity->division,
                'section' => $activity->section,
                'unit' => $activity->unit,
                'status' => $activity->status,
                'year' => $activity->year,
                'user_id' => $activity->user_id,
                'creator_name' => $activity->creator_name,
                'created_at' => $activity->created_at,
            ],
            'logs' => $logs,
            'sub_activity' => $subActivities->map(function ($subActivity) {
                return [
                    'sub_activity_id' => $subActivity->id, // Include sub_activity_id
                    'sub_activity_name' => $subActivity->sub_activity_name,
                    'start_date' => $subActivity->start_date,
                    'completion_date' => $subActivity->completion_date,
                    'routine_non' => $subActivity->routine_non,
                    'workers_involved' => $subActivity->workers_involved,
                    'hazards' => $subActivity->hazards->map(function ($hazard) {
                        return [
                            'hazard_id' => $hazard->id, // Include hazard_id
                            'hazard_name' => $hazard->hazard_name,
                            'gross_likelihood' => $hazard->gross_likelihood,
                            'g_impact' => $hazard->g_impact,
                            'g_ranking' => $hazard->g_ranking,
                            'g_ranking_value' => $hazard->g_ranking_value,
                            'existing_control' => $hazard->existing_control,
                            'further_action_required' => $hazard->further_action_required,
                            'mitigation_measures' => $hazard->mitigation_measures,
                            'residual_likelihood' => $hazard->residual_likelihood,
                            'residual_impact' => $hazard->residual_impact,
                            'residual_ranking' => $hazard->residual_ranking,
                            'residual_ranking_value' => $hazard->residual_ranking_value,
                        ];
                    }),
                ];
            }),
        ];

        return response()->json($response, 200);
    }

    public function getHiraForms()
    {
        $form = DB::table('hira_forms')
            ->select('hira_forms.id', 'hira_forms.name', 'hira_forms.category', 'hira_forms.column_value')
            ->get();
        return response()->json($form);
    }

    public function deleteActivity($id)
    {
        $activity = Activity::with('subActivities.hazards')->find($id);

        if (!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        try {
            DB::transaction(function () use ($activity) {
                // Log deletion BEFORE deleting the activity
                \App\Models\ActivityLog::create([
                    'activity_id' => $activity->id,
                    'activity' => "Activity '{$activity->activity_name}' was deleted on " . now()->timezone('GMT+6')->format('D, M d, Y g:i A'),
                ]);

                // Delete the activity (subActivities and hazards will cascade)
                $activity->delete();
            });

            return response()->json(['message' => 'Activity deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting activity', 'error' => $e->getMessage()], 500);
        }
    }

    public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $functional_type = $request->input('functional_type');
        $approved_by = $request->input('approved_by');
        $approvedOrReject = $request->input('approvedOrReject');

        if ($functional_type === 'EAI') {
            $model = EAI::find($id);
        } elseif ($functional_type === 'HIRA') {
            $model = Activity::find($id);
            ActivityLog::create([
                'activity_id' => $id,
                'activity' => "{$approved_by} {$approvedOrReject} {$functional_type} on " . now()->timezone('GMT+6')->format('D, M d, Y g:i A'),
            ]);
        } else {
            $model = ArrRisk::find($id);
        }
        if (!$model) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        $model->status = $status;
        $model->save();
        return response()->json(['message' => 'You have Reviewed Successfully']);
    }

    public function autoApprove()
    {
        try {
            ActivityLog::info('Auto-approval task is running.');

            // Fetch activities awaiting approval
            $activities = Activity::where('status', 'Awaiting IMS Focal\'s Approval')->get();

            if ($activities->isEmpty()) {
                ActivityLog::info('No activities found for auto-approval.');
                return;
            }

            foreach ($activities as $activity) {
                $activity->status = 'Awaiting Head\'s Approval';
                $activity->save();

                ActivityLog::info("Auto-approved activity ID: {$activity->id}");
            }

            ActivityLog::info('Auto-approval task completed.');
        } catch (\Exception $e) {
            // Log any errors
            ActivityLog::error('Auto-approval task failed: ' . $e->getMessage());
        }
    }

    public function addNewField(Request $request)
    {
        $tableName = 'hiras';
        $columnName = $request->input('column_value');
        $columnType = 'string';
        $isNullable = true;
        if (!Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName, $columnType, $isNullable) {
                $column = $table->$columnType($columnName);
                if ($isNullable) {
                    $column->nullable();
                }
            });
            $newForm = new HiraForm();
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

        if (Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
            DB::table('hira_forms')->where('id', $form_id)->delete();
            return response()->json(['message' => 'Field deleted successfully.'], 200);
        }
        return response()->json(['message' => 'The ' . $columnName . ' does not exist in the table.'], 400);
    }
}
