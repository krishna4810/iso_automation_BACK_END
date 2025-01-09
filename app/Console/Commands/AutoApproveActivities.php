<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Activity;
use App\Models\ActivityLog;

class AutoApproveActivities extends Command
{
    protected $signature = 'activities:auto-approve';
    protected $description = 'Auto approve activities after 4 working days for each role';

    public function __construct()
    {
        parent::__construct();
    }

//    public function handle()
//    {
//        // Current date
//        $currentDate = Carbon::now();
//
//        // Fetch all activities that need auto-approval
//        $activities = Activity::where('status', 'LIKE', 'Awaiting%Approval')->get();
//
//        foreach ($activities as $activity) {
//            $createdAt = Carbon::parse($activity->created_at);
//
//            // Calculate days difference excluding weekends
//            $workingDaysPassed = $this->calculateWorkingDays($createdAt, $currentDate);
//
//            // Determine status based on working days passed
//            if ($workingDaysPassed >= 4 && $activity->status === "Awaiting IMS Focal's Approval") {
//                $this->updateStatus($activity, "Awaiting Head's Approval");
//            } elseif ($workingDaysPassed >= 8 && $activity->status === "Awaiting Head's Approval") {
//                $this->updateStatus($activity, "Awaiting Accepting Officer's Approval");
//            } elseif ($workingDaysPassed >= 12 && $activity->status === "Awaiting Accepting Officer's Approval") {
//                $this->updateStatus($activity, "Awaiting CAU Head's Approval");
//            } elseif ($workingDaysPassed >= 16 && $activity->status === "Awaiting CAU Head's Approval") {
//                $this->updateStatus($activity, "Approved by CAU Head");
//            }
//        }
//
//        $this->info('Auto-approve activities completed successfully.');
//    }
//
//    private function calculateWorkingDays($start, $end)
//    {
//        $workingDays = 0;
//
//        // Loop through days between start and end
//        while ($start->lte($end)) {
//            if (!$start->isWeekend()) {
//                $workingDays++;
//            }
//            $start->addDay();
//        }
//
//        return $workingDays;
//    }
//
//    private function updateStatus($activity, $newStatus)
//    {
//        $activity->status = $newStatus;
//        $activity->save();
//
//        // Log the status update
//        \App\Models\ActivityLog::create([
//            'activity_id' => $activity->id,
//            'activity' => "Auto-approved to {$newStatus} on " . now()->timezone('GMT+6')->format('D, M d, Y g:i A'),
//        ]);
//    }

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

}
