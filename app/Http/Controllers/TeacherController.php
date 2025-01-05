<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::orderByDesc('id')->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return back()->withErrors(['email' => 'Email not found']);
        }

        if($user->hasRole('teacher')) {
            return back()->withErrors(['email' => 'User already has a teacher role']);
        }

        Teacher::create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        if ($user->hasRole('student')) {
            $user->removeRole('student');
        }

        $user->assignRole('teacher');

        return redirect()->route('admin.teachers.index');
    }

    /** 
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->delete();

            $user = \App\Models\User::find($teacher->user_id);
            $user->removeRole('teacher');
            $user->assignRole('student');

            return redirect()->route('admin.teachers.index');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.teachers.index')->with('error', 'Failed to delete teacher.');
        }
    }
}
