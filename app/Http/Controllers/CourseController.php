<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Teacher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Course::with(['category', 'teacher', 'students'])->orderByDesc('id');

        if($user->hasRole('teacher')) {
            $query->whereHas('teacher', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();

        if (!$teacher) {
            return redirect()->route('admin.courses.index')->withErrors('You are not a teacher');
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        } else {
            $thumbnailPath = 'thumbnails/icon-default.png';
        }

        // Category::create($request->all());
        $course = Course::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'about' => $request->about,
            'path_trailer' => $request->path_trailer,
            'thumbnail' => $thumbnailPath,
            'category_id' => $request->category_id,
            'teacher_id' => $teacher->id,
        ]);

        if(!empty($request['course_keypoints'])) {
            foreach ($request['course_keypoints'] as $keypoint) {
                $course->course_keypoints()->create([
                    'name' => $keypoint
                ]);
            }
        }

        return redirect()->route('admin.courses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        DB::beginTransaction();

        try {
            $course->delete();

            DB::commit();

            return redirect()->route('admin.courses.index');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.courses.index')->with('error', 'Failed to delete course.');
        }
    }
}
