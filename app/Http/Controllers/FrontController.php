<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class FrontController extends Controller
{
    public function index() {
        return view('front.index');
    }

    public function details(Course $course) {
        return view('front.details');
    }
}
