<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cookie;

class LanguageController extends Controller
{
    public function change()
    {
        $lang = Cookie::get('lang');

        if($lang=="lv"){
            return back()->withCookie(cookie()->forever('lang', 'en'));
        }
        else return back()->withCookie(cookie()->forever('lang', 'lv'));
    }
}
