<?php

namespace App\Http\Controllers;

use App\Events\EspEvent;
use App\Models\Esp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;
use Pusher\Pusher;

class EspController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data if necessary
        $validatedData = $request->validate([
            'lang' => 'required|numeric|between:-90.0,90.0',
            'lat' => 'required|numeric|between:-180.0,180.0',
            'battery_percentage' => 'required|numeric|min:0|max:100',
            'name' => 'required',
            'is_online' => 'required|boolean',
        ]);
        $esp = ESP::create([
            'lang' => $request->input('lang'),
            'lat' => $request->input('lat'),
            'battery_percentage' => $request->input('battery_percentage'),
            'name' => $request->input('name'),
            'is_online' => $request->input('is_online'),
            'user_id' => auth()->user()->id,
            // Add other fields as needed
        ]);
        broadcast(new EspEvent($esp))->toOthers();

        // Return a response indicating success
        return response()->json(['message' => 'ESP data stored successfully', 'esp' => $esp], 201);
    }

    public function authenticate(Request $request)
    {

        if (!Auth::check()) {
            return response('Unauthorized', 401);
        }

        $user = Auth::user();
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');



        // Extract the user ID from the channel name
        $espId = substr($channelName, 12);
        $espId = intval($espId);

        $esp = Esp::where('id', $espId)->first();

        $userId = $esp->user_id;
        if ((int)$user->id !== (int)$userId) {
            return response('Forbidden', 403);
        }

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID')
        );

        $auth = $pusher->socket_auth($channelName, $socketId);

        return response($auth);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data if necessary
        $validatedData = $request->validate([
            'lang' => 'required|numeric|between:-90.0,90.0',
            'lat' => 'required|numeric|between:-180.0,180.0',
            'battery_percentage' => 'required|numeric|min:0|max:100',
            'name' => 'nullable',
            'is_online' => 'required|boolean',
        ]);
        $esp = Esp::where('id', $id)->first();

        // Update the ESP with the new data
        $esp->update([
            'lang' => $request->input('lang'),
            'lat' => $request->input('lat'),
            'battery_percentage' => $request->input('battery_percentage'),
            'name' => $request->input('name'),
            'is_online' => $request->input('is_online'),
            // Add other fields as needed
        ]);

        // Broadcast the EspEvent to other clients
        broadcast(new EspEvent($esp))->toOthers();

        // Return a response indicating success
        return response()->json(['message' => 'ESP data updated successfully', 'esp' => $esp], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Esp $esp)
    {
        //
    }
}
