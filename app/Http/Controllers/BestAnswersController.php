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
        $answer->question->update([
            'best_answer_id' => $answer->id
        ]);

        return back();
    }
}
