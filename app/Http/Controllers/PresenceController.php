<?php

namespace App\Http\Controllers;

use App\Models\Meetings;
use App\Models\Presence;
use App\Http\Requests\PresenceRequest;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\UpdatePresenceRequest;

class PresenceController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('presence.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('presence.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePresenceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $presence = $request->validate([
            'nim' => 'required|exists:users,nim',
            'meeting_id' => 'required|exists:meetings,id',
            'presence_date' => 'required|date',
            'status' => 'required|',
            'ket' => 'sometimes|string|max:255',
            'feedback' => 'required|string|max:1000',
            'token' => 'required|string|max:10|exists:meetings,token',
        ]);
        $cekToken = Meetings::where('token', $request->token)->firstOrFail();

        if($cekToken) {
            Presence::create($presence);
            return redirect()->route('presence.index')->with('success', 'Presensi berhasil');
        } else {
            return redirect()->back()->with('error', 'Presensi gagal, cek kembali token');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function show(Presence $presence){
        return view('presence.show', [
            'presence' => $presence
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function edit(Presence $presence){
        return view('presence.edit', [
            'presence' => $presence
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePresenceRequest  $request
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Presence $presence) {
        $rule = [
            'nim' => 'required|exists:users,nim',
            'meetings_id' => 'required|exists:meetings,id',
            'presence_date' => 'required|date',
            'status' => 'required',
            'ket' => 'sometimes|string|max:255',
            'feedback' => 'required|string|max:1000',
            'token' => 'required|string|max:10|exists:meetings,token',
        ];
        $validated = $request->validate($rule);
        Presence::where('nim', $presence->nim)->update($validated);
        return redirect()->route('presence.index')->with('success', 'Presence updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Presence $presence) {
        Presence::destroy($presence->user->nim);
        return redirect()->route('presence.index')->with('success', 'Presence deleted successfully.');
    }
}
