<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Establishment;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Eloquent\Support\Collection;

class EventController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }

    public function getEventByUserCity($id){
        /*
         * TODO:
         * 1. Get user city
         * 2. Get all events from that city
         * 3. Return events
         *
         */

        $user = User::find($id);
        dd($user);
        $barathonien = $user->barathoniens;
        $establishments = Establishment::all()->where("city", "=", $barathonien->city);
        $lesevent = $establishments[0]->events;

        $collections = collect();

        $establishments->each(function($establish, $key) use($collections){
            $events = $establish->events;
            $events->each(function($event, $key) use($collections){
                $collections->push($event);
            });
        });

        return $this->success([
            'event' => $collections,
        ]);

    }
}
