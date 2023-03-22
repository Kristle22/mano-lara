<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master;

class MasterJsController extends Controller
{
    const RESULTS_IN_PAGE = 5;

    public function index()
    {
        return view('master_js.index');
    }

    public function list()
    {
        $masters = Master::orderBy('created_at', 'desc')->paginate(self::RESULTS_IN_PAGE)->withQueryString();

        $html = view('master_js.list', compact('masters'))->render();

        return response()->json([
            'html' => $html
        ]);
    }

    public function create()
    {
        $html = view('master_js.create')->render();
        
        return response()->json([
            'html' => $html
        ]);
    }

    public function store(Request $request)
    {
        $master = new Master;
        $master->name = $request->master_name;
        $master->surname = $request->master_surname;
        $master->save();
        
        $msgHtml = view('master_js.messages', ['successMsg' => 'Valio, naujas meistras sėkmingai atvyko!'])->render(); 

        return response()->json([
            'hash' => 'list',
            'msg' => $msgHtml
        ]); 
    }

    public function edit(Master $master)
    {
        $html = view('master_js.edit', compact('master'))->render();

        return response()->json(compact('html'));
    }

    public function update(Request $request, Master $master)
    {
        $master->name = $request->master_name;
        $master->surname = $request->master_surname;
        $master->save();

        return response()->json([
            'hash' => 'list'
        ]);
    }

    public function destroy(Master $master)
    {
        if($master->getOutfits->count()){
            $msgHtml = view('master_js.messages', ['infoMsg' => 'Nope! Šio meistro ištrinti negalima, nes jis turi užsakymų.'])->render();

            return response()->json([
                'msg' => $msgHtml
            ]);
        }
        $master->delete();

        $msgHtml = view('master_js.messages', ['successMsg' => 'Meistras sėkmingai ištrintas.'])->render();

        return response()->json([
            'hash' => 'list',
            'msg' => $msgHtml
        ]);
    }
}
