<?php

namespace App\Http\Controllers;

use App\Filters\QuestionFilter;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show','index']);
        $this->middleware('must-verify-email')->except(['index', 'show']);
    }
    public function index(Category $category, QuestionFilter $filters)
    {
        if ($category->exists) {
            $questions = Question::published()->where('category_id', $category->id);
        } else {
            $questions = Question::published();
        }

        $questions = $questions->filter($filters)->paginate(20);

        array_map(function (&$item) {
            return $this->appendAttribute($item);
        }, $questions->items());

        return view('questions.index', [
            'questions' => $questions
        ]);
    }
    public function store()
    {
        $this->validate(request(), [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);
        Question::create([
            'user_id' => auth()->id(),
            'category_id' => request('category_id'),
            'title' => request('title'),
            'content' => request('content'),
        ]);

        return redirect("/drafts")->with('flash', '保存成功！');
    }
    public function show($category, $questionId)
    {
        $question = Question::published()->findOrFail($questionId);

        $answers = $question->answers()->paginate(20);

        array_map(function ($item) {
            return $this->appendVotedAttribute($item);
        }, $answers->items());

        return view('questions.show', [
            'question' => $question,
            'answers' => $answers
        ]);
    }
    public function create(Question $question)
    {
        $categories = Category::all();

        return view('questions.create', [
            'question' => $question,
            'categories' => $categories
        ]);
    }

    protected function appendAttribute($item)
    {
        $user = Auth::user();

        $item->isVotedUp = $item->isVotedUp($user);
        $item->isVotedDown = $item->isVotedDown($user);
        $item->isSubscribedTo = $item->isSubscribedTo($user);

        return $item;
    }
}
