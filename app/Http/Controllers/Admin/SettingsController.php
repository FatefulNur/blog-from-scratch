<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin-only, \App\Models\User']);
    }

    public function general()
    {
        $generals = Setting::select('site_name', 'tagline', 'membership', 'default_role')->firstOrNew();
        return view('admin.settings.general', compact('generals'));
    }

    public function updateGeneral(Request $request)
    {
        $update = Setting::updateOrCreate(['id' => 1], [
            'site_name' => $request->input('site_name'),
            'tagline' =>  $request->input('tagline'),
            'membership' => ($request->input('membership') == "on") ? 1 : 0,
            'default_role' => $request->input('default_role') ?: 2
        ]);

        if($update)
        return back()->with('action', 'General settings updated');
    }

    public function comment()
    {
        $comment = Setting::firstOrNew();
        return view('admin.settings.comment', compact('comment'));
    }

    public function updateComment(Request $request)
    {
        $update = Setting::updateOrCreate(['id' => 1], [
            'allow_comment' => ($request->input('allow_comment') == "on") ? 1 : 0,
            'nested_comment' => ($request->input('nested_comment') == "on") ? 1 : 0,
            'max_depth_comment' => $request->input('max_depth_comment'),
            'comment_permission' => ($request->input('comment_permission') == "on") ? 1 : 0
        ]);

        if($update)
        return back()->with('action', 'Comment settings updated');
    }
}
