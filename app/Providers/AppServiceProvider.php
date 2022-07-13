<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Question;
use App\Observers\QuestionObserver;
use App\Translator\BaiduSlugTranslator;
use App\Translator\Translator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.debug')) {
            $this->app->register('VIACreative\SudoSu\ServiceProvider');
        }
        $this->app->bind(Translator::class, BaiduSlugTranslator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \Illuminate\Pagination\Paginator::useBootstrap();
        Question::observe(QuestionObserver::class);
        \View::composer('*',function ($view){
            $view->with('categories', Category::all());
        });
    }
}
