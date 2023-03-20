<?php

namespace App\Http\Controllers;

use App\Models\Outfit;
use App\Models\Master;
use App\Http\Requests\StoreOutfitRequest;
use App\Http\Requests\UpdateOutfitRequest;
use Validator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Intervention\Image\ImageManagerStatic as Image;

class OutfitController extends Controller
{
    const RESULTS_IN_PAGE = 9;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $masters = Master::paginate(self::RESULTS_IN_PAGE)->withQueryString();

        if ($request->sort) {
            if ('type' == $request->sort && 'asc' == $request->sort_dir) {
                $outfits = Outfit::orderBy('type')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            }
            else if ('type' == $request->sort && 'desc' == $request->sort_dir) {
                $outfits = Outfit::orderBy('type', 'desc')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            }
            else if ('color' == $request->sort && 'asc' == $request->sort_dir) {
                $outfits = Outfit::orderBy('color')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            }
            else if ('color' == $request->sort && 'desc' == $request->sort_dir) {
                $outfits = Outfit::orderBy('color', 'desc')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            }
            else if ('size' == $request->sort && 'asc' == $request->sort_dir) {
                $outfits = Outfit::orderBy('size')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            }
            else if ('size' == $request->sort && 'desc' == $request->sort_dir) {
                $outfits = Outfit::orderBy('size', 'desc')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            }
            else {
                $outfits = Outfit::paginate(self::RESULTS_IN_PAGE)->withQueryString();  
            }
        }
        else if ($request->filter && 'master' == $request->filter) {
            $outfits = Outfit::where('master_id', $request->master_id)->paginate(self::RESULTS_IN_PAGE)->withQueryString();
        }
        else if ($request->search && 'all' == $request->search) {

            $words = explode(' ', $request->s);
            // dd($words);
            if (count($words) == 1) {
            $outfits = Outfit::where('color', 'like', '%'.$request->s.'%')
            ->orWhere('type', 'like', '%'.$request->s.'%')
            ->orWhere('size', 'like', '%'.$request->s.'%')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            } else {
                $outfits = Outfit::where(function($query) use ($words) {
                    $query->where('color', 'like', '%'.$words[0].'%')
                    ->orWhere('type', 'like', '%'.$words[0].'%')
                    ->orWhere('size', 'like', '%'.$words[0].'%');
                    })
                ->where(function($query) use ($words) {
                $query->where('color', 'like', '%'.$words[1].'%')
                ->orWhere('type', 'like', '%'.$words[1].'%')
                ->orWhere('size', 'like', '%'.$words[1].'%');
                })->paginate(self::RESULTS_IN_PAGE)->withQueryString();
            }
        }
        else{
            // sortinam pagal sukurimo laika
            $outfits = Outfit::orderBy('created_at', 'desc')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
        }
       
        return view('outfit.index', [
            'outfits' => $outfits, 
            'sortDirection' => $request->sort_dir ?? 'asc', 
            'masters' => $masters, 
            'masterId' => $request->master_id ?? '0', 
            's' => $request->s ?? ''
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $masters = Master::orderBy('surname')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
        return view('outfit.create', ['masters' => $masters]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOutfitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOutfitRequest $request)
    {
        $validator = Validator::make($request->all(),
        [
            'outfit_type' => ['required', 'min:3', 'max:50'],
            'outfit_color' => ['required', 'min:3', 'max:20'],
            'outfit_size' => ['required', 'integer', 'min:5', 'max:22'],
            'outfit_about' => ['required'],
            'master_id' => ['required', 'integer', 'min:1'],
            'outfit_photo' => ['sometimes', 'image']
        ],
 [
 'outfit_size.min' => 'Outfit size begins from number 5.'
 ]
        );
        if ($validator->fails()) {
            $request->flash();
            return redirect()->back()->withErrors($validator);
        }
        
        $outfit = new Outfit;

        $file = $request->file('outfit_photo');
        if ($file) {
            $ext = $file->getClientOriginalExtension();
            $name = rand(1000000, 9999999).'_'.rand(1000000, 9999999);
            $name .= '.'.$ext;

            $destinationPath = public_path().'/outfits-images/';
            $file->move($destinationPath, $name);
            $outfit->photo = asset('/outfits-images/'.$name);
            
            // image intervention
            $img = Image::make($destinationPath.$name);
            $img->gamma(5.6)->flip('v');
            $img->save($destinationPath.$name);
        }

        $outfit->type = $request->outfit_type;
        $outfit->color = $request->outfit_color;
        $outfit->size = $request->outfit_size;
        $outfit->about = $request->outfit_about;
        $outfit->master_id = $request->master_id;
        $outfit->save();
        return redirect()->route('outfit.index')->with('success_message', 'New outfit has been created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outfit  $outfit
     * @return \Illuminate\Http\Response
     */
    public function show(Outfit $outfit)
    {
        return view('outfit.show', compact('outfit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Outfit  $outfit
     * @return \Illuminate\Http\Response
     */
    public function edit(Outfit $outfit)
    {
        $masters = Master::orderBy('surname')->paginate(self::RESULTS_IN_PAGE)->withQueryString();
        return view('outfit.edit', ['masters' => $masters], compact('outfit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOutfitRequest  $request
     * @param  \App\Models\Outfit  $outfit
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOutfitRequest $request, Outfit $outfit)
    {
        $validator = Validator::make($request->all(),
        [
            'outfit_type' => ['required', 'min:3', 'max:50'],
            'outfit_color' => ['required', 'min:3', 'max:20'],
            'outfit_size' => ['required', 'integer', 'min:5', 'max:22'],
            'outfit_about' => ['required'],
            'master_id' => ['required', 'integer', 'min:1'],
            'outfit_photo' => ['sometimes', 'image']
        ],
 [
 'outfit_surname.min' => 'Surname must consists at least of 2 characters.'
 ]
        );
        if ($validator->fails()) {
            $request->flash();
            return redirect()->back()->withErrors($validator);
        }

        $file = $request->file('outfit_photo');

        if ($file) {
            $ext = $file->getClientOriginalExtension();
            $name = rand(1000000, 9999999).'_'.rand(1000000, 9999999);
            $name .= '.'.$ext;
            $destinationPath = public_path().'/outfits-images/';

            $file->move($destinationPath, $name);

            $oldPhoto = $outfit->photo ?? '@@@';
            $outfit->photo = asset('/outfits-images/'.$name);

            // Trinam sena, jeigu ji yra
            $oldName = explode('/', $oldPhoto);
            $oldName = array_pop($oldName);
            if (file_exists($destinationPath.$oldName)) {
                unlink($destinationPath.$oldName);
            }
        }
        if ($request->outfit_photo_deleted) {
            $destinationPath = public_path().'/outfits-images/';
            $oldPhoto = $outfit->photo ?? '@@@';
            $outfit->photo = null;
            $oldName = explode('/', $oldPhoto);
            $oldName = array_pop($oldName);
            if (file_exists($destinationPath.$oldName)) {
                unlink($destinationPath.$oldName);
            }
        }

        $outfit->type = $request->outfit_type;
        $outfit->color = $request->outfit_color;
        $outfit->size = $request->outfit_size;
        $outfit->about = $request->outfit_about;
        $outfit->master_id = $request->master_id;
        $outfit->save();
        return redirect()->route('outfit.index')->with('success_message', 'The outfit has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Outfit  $outfit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outfit $outfit)
    {
        $destinationPath = public_path().'/outfits-images/';
        $oldPhoto = $outfit->photo ?? '@@@';

        // Trinam sena, jeigu ji yra
        $oldName = explode('/', $oldPhoto);
        $oldName = array_pop($oldName);
        if (file_exists($destinationPath.$oldName)) {
            unlink($destinationPath.$oldName);
         }

        $outfit->delete();
        return redirect()->route('outfit.index')->with('success_message', 'The outfit has been deleated.');
    }

    public function pdf(Outfit $outfit) {
        $pdf = Pdf::loadView('outfit.pdf', compact('outfit'));
        return $pdf->download(ucfirst($outfit->color).'-'.$outfit->type.'.pdf');
    }
}
