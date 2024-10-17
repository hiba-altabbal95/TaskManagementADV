<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateDailyReport;
use App\Mail\DailyReportMail;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function generateDailyReport()
    {
        // Fetch tasks for the report
        $tasks = Task::where('status', 'Completed')->get();

        // Generate the report content
        $reportContent = "Daily Report\n\n";
        foreach ($tasks as $task) {
            $reportContent .= "Task ID: {$task->id}\n";
            $reportContent .= "Title: {$task->title}\n";
            $reportContent .= "Description: {$task->description}\n";
            $reportContent .= "Assigned To: {$task->assigned_to}\n";
            $reportContent .= "Due Date: {$task->date_due}\n";
            $reportContent .= "Priority: {$task->priority}\n";
            $reportContent .= "Status: {$task->status}\n\n";
        }

        // Save the report to a file
        $fileName = 'daily_report_' . now()->format('Y_m_d') . '.txt';
        Storage::disk('local')->put($fileName, $reportContent);

        // Send the report via email
        $filePath = storage_path('app/' . $fileName);
        Mail::to('hiba.altabbal95@gmail.com')->send(new DailyReportMail($filePath));
    }

    
   
     
    

     public function dailyReport()
     {
         // This method can be used to manually trigger the report generation
         $this->generateDailyReport();
         
         return response()->json(['message' => 'Daily report generated and sent successfully']);
     }
   

     public function triggerDailyReport()
    {
        // Dispatch the job
        GenerateDailyReport::dispatch();

        return response()->json(['message' => 'Daily report job dispatched successfully']);
    }
   

  
}
