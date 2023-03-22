<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Http\Requests\StoreMasterRequest;
use App\Http\Requests\UpdateMasterRequest;
use Validator;

class MasterController extends Controller
{
    const RESULTS_IN_PAGE = 5;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $masters = Master::all();
        $masters = Master::orderBy('surname')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
        // $masters = $masters->sortByDesc('surname');

        return view('master.index', ['masters' => $masters]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMasterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMasterRequest $request)
    {
         $validator = Validator::make($request->all(),
       [
           'master_name' => ['required', 'min:3', 'max:64'],
           'master_surname' => ['required', 'min:3', 'max:64'],
       ],
[
'master_surname.min' => 'Surname must consists at least of 2 characters.'
]
       );
       if ($validator->fails()) {
           $request->flash();
           return redirect()->back()->withErrors($validator);
       }
        $master = new Master;
        $master->name = $request->master_name;
        $master->surname = $request->master_surname;
        $master->save();
        return redirect()->route('master.index')->with('success_message', 'New master has arrived!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function show(Master $master)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function edit(Master $master)
    {
        return view('master.edit', ['master' => $master]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMasterRequest  $request
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMasterRequest $request, Master $master)
    {
        $validator = Validator::make($request->all(),
        [
            'master_name' => ['required', 'min:3', 'max:64'],
            'master_surname' => ['required', 'min:3', 'max:64'],
        ],
 [
 'master_surname.min' => 'Surname must consists at least of 2 characters.'
 ]
        );
        if ($validator->fails()) {
            $request->flash();
            return redirect()->back()->withErrors($validator);
        }

        $master->name = $request->master_name;
        $master->surname = $request->master_surname;
        $master->save();
        return redirect()->route('master.index')->with('success_message', 'The master has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function destroy(Master $master)
    {
        if($master->getOutfits->count()){
            return redirect()->back()->with('info_message', 'Nope! You can\'t delete this master, cause he has some outfits.');
        }
        $master->delete();
        return redirect()->route('master.index')->with('success_message', 'The master has gone.');
    }
}
