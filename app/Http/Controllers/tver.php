<?php

namespace App\Http\Controllers;

use App\Models\tv_list;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class tver extends Controller
{
    public function get_all(Request $request) {
        $halaman = $request->header('page', 0);
        $sorts = $request->header('sort', 'created_at');
        $tipe = $request->header('by', 'asc');
        return response()->json(tv_list::orderBy($sorts, $tipe)->skip($halaman * 5)->take(5)->get(),200);
    }

    public function up_tv(Request $request) {
        // dd($request->all());
        $valid = Validator::make($request->all(), [
            'acara' => 'required',
            'thumb' => 'required',
            'category' => 'sometimes',
            'link' => 'required',
            'status' => 'sometimes',
        ]);

        // wajib ada acara,thumb/thumnail, link streaming
        // optional category dan status (format enum)

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        }

        $created = tv_list::create([
            'acara' => $request->acara,
            'thumb' => $request->thumb,
            'link' => $request->link,
            'rate' => 0
        ]);
        $optional = $request->only(['category', 'status']);
        $created->update($optional);

        return response()->json($created,200);
    }

}
