<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Create a new report.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'description' => 'required',
        ]);

        $report = new Report();
        $report->user_id = Auth::id();
        $report->song_id = $request->route('id');
        $report->description = $request->description;

        $report->save();

        return response()->json([
            'success' => true,
            'report' => $report
        ], 201);
    }

    /**
     * Display a listing of reports.
     *
     * @return Response
     */
    public function index()
    {
        $report = Report::get(['user_id', 'song_id', 'description'])->toArray();
        return $report;
    }

    /**
     * Display specified report
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, report with id ' . $id . ' cannot be found.'
            ], 400);
        }

        $currentReport = $report->get(['user_id', 'song_id', 'description'])->toArray();

        return response()->json([
            'success' => true,
            'report' => $currentReport
        ]);
    }
}
