<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\EventCalendar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventCalendarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $year = date('Y');

        if ($request->query('year') !== null) {
            $year = $request->query('year');
        }

        $events = EventCalendar::whereYear('date', $year)->orderBy('date')->get();
        return view('event-calendars.index', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $name = $request->name;
            $date = $request->date;
            $type = $request->type;

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('event_calendars')->where(function ($query) use ($name, $date) {
                        return $query->where('name', $name)
                            ->where('date', $date);
                    }),
                    'max:255'
                ],
                'date' => [
                    'required',
                    'date',
                ],
                'type' => ['required', 'max:255'],
            ]);

            $event = new EventCalendar();
            $event->name = ucwords($name);
            $event->date = $date;
            $event->type = $type;
            $event->save();

            return response()->json([
                'message' => 'Data telah tersimpan',
                'data' => $event,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $name = $request->name;
            $date = $request->date;
            $type = $request->type;

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('event_calendars')->where(function ($query) use ($name, $date) {
                        return $query->where('name', $name)
                            ->where('date', $date);
                    }),
                    'max:255'
                ],
                'date' => [
                    'required',
                    'date',
                ],
                'type' => ['required', 'max:255'],
            ]);

            $event = EventCalendar::find($id);
            $event->name = ucwords($name);
            $event->date = $date;
            $event->type = $type;
            $event->save();

            return response()->json([
                'message' => 'Data telah tersimpan',
                'data' => $event,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $company = EventCalendar::findOrFail($id);
            $company->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }
}
