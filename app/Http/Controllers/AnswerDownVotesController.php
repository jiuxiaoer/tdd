<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Auth;
use Illuminate\Http\Request;

class AnswerDownVotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Answer $answer)
    {
        $answer->voteDown(Auth::user());

        return response([], 201);
    }
}
