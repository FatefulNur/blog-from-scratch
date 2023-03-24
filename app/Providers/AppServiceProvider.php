<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer("components.home.footer", function ($view) {
            $categories = Category::all();
            $tags = Tag::all();

            $view->with("categories", $categories)->with("tags", $tags);
        });

        View::composer(["components.home.template", "components.home.header", "components.home.footer"], function ($view) {
            $name = Str::lower(\App\Models\Setting::firstOrNew(['id' => 1])->site_name);
            $_this = Str::of($name)->before("blog");
            $with = "<span class='text-primary'>$_this</span>";
            $siteName = Str::of($name)->replaceFirst($_this, $with);

            $view->with('siteName', $siteName);
        });
    }
}
