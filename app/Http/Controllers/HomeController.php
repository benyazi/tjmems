<?php

namespace App\Http\Controllers;

use App\Model\Mem;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function rate(Request $request, $type = null)
    {
        if(!in_array($type, [null, 'comments'])) {
            abort(404, 'Я не нашел такой рейтинг :( ');
        }
        $rateType = "лайкам";
        $sortBy = 'likes';
        if($type == "comments") {
            $rateType = "комментариям";
            $sortBy = 'commentCount';
        }

        $mems = Mem::query()
            ->where('isMem', true)
            ->orderBy($sortBy, 'DESC')
            ->get();

        return view('rate', [
            'mems' => $mems,
            'rateType' => $rateType
        ]);
    }
}
