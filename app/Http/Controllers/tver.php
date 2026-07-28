<?php

namespace App\Http\Controllers;

use App\Models\favorites;
use App\Models\reviews;
use App\Models\tv_list;
use App\Models\watch_history;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
            $user = $request->user('sanctum');
            $channel->update([
                'watched' => $channel->watched + 1
            ]);
            $watched= [];
            if ($user) {
                $watched = watch_history::where('user_id', $user->id)->where('tv_id', $id)->first();
            }
            if ($user && !$watched) {
                watch_history::create([
                'user_id' => $user->id,
                'tv_id' => $id,
                'duration_watched' => 0,
                'last_watched_at' => Carbon::now()
            ]);
            } elseif ($watched) {
                $watched->update([
                    'last_watched_at' => Carbon::now()
                ]);
            }
            return response()->json($channel);
        } elseif ($category) {
            $kats = tv_list::where('category', $category)->get();
            $country ? $kats->where('country', $country) : $kats;
            return response()->json($kats ? $kats : 'kategori tidak ditemukan');
        } elseif ($country) {
            return response()->json(tv_list::where('country', $country)->get());
        }
        return response()->json(tv_list::orderBy($sorts, $tipe)->skip($halaman * 5)->take(5)->get(),200);
    }

    public function add_duration_watch(Request $request,$id) {
        watch_history::where('user_id', $request->user()->id)->where('tv_id', $id)->first()->update([
            'duration_watched' => DB::raw("ADDTIME(duration_watched, '00:01:00')")
        ]);
    }

    public function rm_history(Request $request) {
        $user = $request->user()->id;
        watch_history::where('user_id',$user)->delete();
        return response()->json([],204);
    }

    public function rm_review(Request $request) {
        $ripiw = reviews::where('id',$request->id)->first();
        if (($request->user()->role == "watcher") && ($ripiw->user_id != $request->user()->id)) {
            return response()->json('aja sendiri',403);
        }
        $ripiw->delete();
        return response()->json([],204);
    }

    public function get_recommend(Request $request) {
        $by_country = $request->header('country',);
        $by_category = $request->header('category',);
        $by_rating = $request->header('rated');

        if ($by_category) {
            return response()->json(tv_list::where('category', $by_category)->inRandomOrder()->take(6)->get());
        } elseif($by_country) {
            return response()->json(tv_list::where('country', $by_country)->orderBy('rate', 'desc')->take(6)->get());
        } elseif($by_rating) {
            return response()->json(tv_list::orderBy('rate', 'desc')->take(10)->get());
        }
        return response()->json(tv_list::inRandomOrder()->limit(19)->get());
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

        $fav = favorites::where('user_id', $request->user()->id)->where('tv_id', $id)->first();

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

    public function tv_edit(Request $request,$id) {
        $tv = tv_list::where('id', $id)->first();
        if (!$tv) {
            return response()->json('tidak ditemukan',404);
        }
        $opsi = $request->only(['acara','thumb','category','country']);
        $tv->update($opsi);
        $tv->update([
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'berhasil',
            'data' => $tv
        ]);
    }

    public function get_user_histories(Request $request) {
        $user = $request->header('id', $request->user()->id);
        if (($request->user()->role == 'wathcer') && ($request->user()->id != $user)) {
            return response()->json('you dont have to do that',403);
        }
        return response()->json(watch_history::where('user_id', $user)->get(),200);
    }

}
