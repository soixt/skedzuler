<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index']);
    }

    public function index (Request $request)
    {
        $request->validate([
            'date' => 'required'
        ]);

        $date = Carbon::createFromFormat('d/m/Y', $request->input('date')); 

        return ScheduleResource::collection(Schedule::whereYear('event_date', '=', $date->year)->whereMonth('event_date', '=', $date->month)->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'description' => 'required'
        ]);

        $event = Schedule::create([
            'user_id' => 1,
            'description' => $request->input('description'),
            'event_date' => Carbon::createFromFormat('d/m/Y', $request->input('date'))
        ]);

        return new ScheduleResource($event);
    }
    
    public function update(UpdateScheduleRequest $request)
    {
        $schedule = Schedule::find($request->input('id'));
        $schedule->description = $request->input('description');
        $schedule->save();

        return new ScheduleResource($schedule);
    }
    
    public function destroy()
    {
        $schedule = Schedule::findOrFail(request('id'));
        // if ($schedule->user_id == auth()->user()->id) {
            $schedule->delete();

            return response()->json([
                'message' => 'Event was deleted successfully!'
            ], 203);
        // }

        return response('Forbidden!', 403);
    }
}
