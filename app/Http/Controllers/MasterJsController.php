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
        $masters = Master::orderBy('surname')->paginate(self::RESULTS_IN_PAGE)->withQueryString();

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
}
