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

        return response()->json([
            'hash' => 'list'
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
        $master->delete();

        return response()->json([
            'hash' => 'list'
        ]);
    }
}
