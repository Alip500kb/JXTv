<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class user_manager extends Controller
{
    public function user_info(Request $request) {
        return response()->json(User::where('name', $request->user()->name)->get(),200);
    }

    public function users_status(Request $request) {
        //butuh header untuk status banned
        $stat = $request->header('status','active');
        return response()->json(User::where('status', $stat)->get(), 200);
    }

    
}
