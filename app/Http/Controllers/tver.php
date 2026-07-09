<?php

namespace App\Http\Controllers;

use App\Models\favorites;
use App\Models\reviews;
use App\Models\tv_list;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isBool;

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
        // $createds = [];
        for ($i = 0; $i <= (count($request->all()) - 1) ; $i++) {
            $valid = Validator::make($request->all()[$i], [
            'name' => 'required',
            'image' => 'required',
            'category' => 'sometimes',
            'url' => 'required',
            'status' => 'sometimes',
            'country' => 'sometimes',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'error' => $valid->errors(),
                'array' => $request->all()[$i]
        ],422);}
        // dd($request[$i]['acara']);
        $created = tv_list::create([
            'acara' => $request->all()[$i]['name'],
            'thumb' => $request->all()[$i]['image'],
            'url' => $request->all()[$i]['url'],
            'rate' => 0
        ]);
        $optional = Arr::only($request->all()[$i],[ 'category', 'status', 'country']);
        // dd($optional);
        $created->update($optional);
        // $createds = $created;
        };

        // dd($request->all());
 
        // wajib ada acara,thumb/thumbnail, link streaming
        // optional category dan status (format enum)

        return response()->json([
            'status' => 'berhasil',
            'jumlah' => count($request->all())
        ],201);
    }

    public function tv_rate(Request $request,$id) {
        $valid = Validator::make($request->all(), [
            'rate' => 'required|numeric',
            'comment' => 'sometimes'
        ]);

        // dd($request->user()->id);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        }

        $channel = tv_list::where('id', $id)->first();
        $cek = reviews::where('user_id', $request->user()->id)->first();
        if (!$channel) {
            return response()->json('apa yang kamu rating bung',404);
        } elseif (($request->rate > 5) || ($request->rate < 1)) {
            return response()->json('iam one step ahead',200);
        } elseif ($cek) {
            $cek->update([
                'rating' => $request->rate
            ]);
            $comment = $request->only('comment');
            $cek->update($comment);
            $channel->update([
                'rate' => reviews::where('tv_id', $id)->get()->average('rating')
            ]);
            
            return response()->json('berhasil',201);
        }

        $rview = reviews::create([
            'user_id' => $request->user()->id,
            'tv_id' => $id,
            'rating' => $request->rate
        ]);
        
        $comment = $request->only('comment');
        $rview->update($comment);

        $channel->update([
            'rate' => reviews::where('tv_id',$id)->get()->average('rating'),
        ]);

        return response()->json('berhasil',201);
    }

    public function favorite(Request $request, $id) {
        $valid = Validator::make($request->all(),[
            'fav' => 'required|boolean'
        ]); 

        $fav = favorites::where('user_id', $request->user()->id)->first();

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif (($fav) && ($request->fav == false)) {
            $fav->delete();
            return response()->json([],204);
        } elseif (!tv_list::where('id', $id)->exists()) {
            return response()->json('channel tidak ditemukan',404);
        } elseif ($fav) {
            return response()->json();
        }

        favorites::create([
            'user_id' => $request->user()->id,
            'tv_id' => $id
        ]);

        return response()->json('berhasil',201);
        
    }
        
}
