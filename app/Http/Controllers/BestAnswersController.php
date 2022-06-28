<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class BestAnswersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Answer $answer)
    {
        $this->authorize('update', $answer->question);
        $answer->question->markAsBestAnswer($answer);
        return back()->with('flash', '已设为最佳答案！');
    }
}
