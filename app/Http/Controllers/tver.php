<?php

namespace App\Http\Controllers;

use App\Models\tv_list;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class tver extends Controller
{
    //memberikan semua hal tentang channel yang akan diberikan ke role watcher
    public function get_all(Request $request) {
        $halaman = $request->header('page', 0);
        $sorts = $request->header('sort', 'created_at');
        $tipe = $request->header('by', 'asc');
        $id = $request->query('id', );
        $category = $request->query('catg',);
        $country = $request->query('country',);
        if ($id) {
            $channel = tv_list::where('id',$id)->first();
            $channel->update([
                'watched' => $channel->watched + 1
            ]);
            return response()->json();
        } elseif ($category) {
            $kats = tv_list::where('category', $category)->get();
            $country ? $kats->where('country', $country) : $kats;
            return response()->json($kats ? $kats : 'kategori tidak ditemukan');
        } elseif ($country) {
            return response()->json(tv_list::where('country', $country)->get());
        }
        return response()->json(tv_list::orderBy($sorts, $tipe)->skip($halaman * 5)->take(5)->get(),200);
    }

    public function get_recommend(Request $request) {
        $by_country = $request->header('country',);
        $by_category = $request->header('category',);

        if ($by_category) {
            return response()->json(tv_list::where('category', $by_category)->random(10)->get());
        } elseif($by_country) {
            return response()->json(tv_list::where('country', $by_country)->random(10)->get());
        }
        return response()->json(tv_list::random(36));   
    }

    public function up_tv(Request $request) {
        // dd($request->all());
        $valid = Validator::make($request->all(), [
            'acara' => 'required',
            'thumb' => 'required',
            'category' => 'sometimes',
            'url' => 'required',
            'status' => 'sometimes',
            'country' => 'sometimes',
        ]);

        // wajib ada acara,thumb/thumbnail, link streaming
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
        $optional = $request->only(['category', 'status', 'country']);
        $created->update($optional);

        return response()->json($created,201);
    }

    public function tv_rate(Request $request,$id) {
        $channel = tv_list::where('id', $id)->first();
        
        if (!$channel) {
            return response()->json('apa yang kamu rating bung',404);
        } elseif ($request->rate > 5) {
            return response()->json('iam one step ahead',200);
        }

        $channel->update([
            'rate' => $request->rating
        ]);

        return response()->json('berhasil',201);
    }

}
