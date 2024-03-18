<?php

namespace App\Http\Controllers;

use App\Events\EspEvent;
use App\Models\Esp;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;

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

        // $factory = (new Factory)
        //     ->withServiceAccount(storage_path('app/firebase/test-76056-firebase-adminsdk-mnxki-3ea1fe3293.json'))
        //     ->withDatabaseUri('https://test-76056-default-rtdb.firebaseio.com');

        // // Create a reference to the Firebase Realtime Database
        // $database = $factory->createDatabase();

        // // Push ESP data to Firebase Realtime Database under 'contacts' node
        // $reference = $database->getReference('esps/' . 26);
        // // Push ESP data to Firebase Realtime Database under 'esps/id' node
        // $reference->set([
        //     'lang' => $esp->lang,
        //     'lat' => $esp->lat,
        //     'battery_percentage' => $esp->battery_percentage,
        //     'name' => $esp->name,
        //     'is_online' => $esp->is_online,
        //     'user_id' => $esp->user_id,
        //     // Add other ESP data fields here
        // ]);



        // Return a response indicating success
        return response()->json(['message' => 'ESP data stored successfully', 'esp' => $esp], 201);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Esp $esp)
    {
        //
    }
}
