<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function appendVotedAttribute($item)
    {
        $item->isVotedUp = $item->isVotedUp(Auth::user());
        $item->isVotedDown = $item->isVotedDown(Auth::user());

        return $item;
    }
}
