<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Notifications\YouWereInvited;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublishedQuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Question $question)
    {

        $this->authorize('update', $question);

        preg_match_all('/@([^\s.]+)/',$question->content,$matches);

        $names = $matches[1];
        // And then notify user
        foreach ($names as $name){
            $user = User::whereName($name)->first();

            if($user){
                $user->notify(new YouWereInvited($question));
            }
        }


        $question->publish();
    }
}
