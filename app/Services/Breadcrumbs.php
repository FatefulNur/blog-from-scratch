<?php

namespace App\Services;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Request;

class Breadcrumbs {

    public function render($home = "/")
    {
        $str = "<a class='breadcrumb-item' href='" . url(RouteServiceProvider::HOME) . "'>{$home}</a>";
        $path = explode('/', trim(Request::path(), '/'));
        $url = "/";
        foreach($path as $p) {
            if($p == last($path)) {
                $str .= "<span class=\"breadcrumb-item\">" . ucwords(str_replace('-', ' ', $p)) . "</span>\n";
            } else {

                $url .= $p . '/';
                $str .= "<a class=\"breadcrumb-item\" href='" . url($url) . "'>" . ucwords(str_replace('-', ' ', $p)) . "</a>\n";
            }
        }

        return $str;
    }

}
