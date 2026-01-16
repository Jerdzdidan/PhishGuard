<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    //
    public function index()
    {
        $lessons = Lesson::get();

        return view('admin.lessons.index', compact($lessons));
    }
}
