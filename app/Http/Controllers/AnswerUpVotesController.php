<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Auth;
use Illuminate\Http\Request;

class AnswerUpVotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Answer $answer)
    {
        $answer->voteUp(Auth::user());

        return response([], 201);
    }
    public function destroy(Answer $answer)
    {
        $answer->cancelVoteUp(Auth::user());

        return response([], 201);
    }
}
