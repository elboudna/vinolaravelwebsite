<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use App\Models\Bouteille;
use App\Models\BouteilleSaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BouteilleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bouteilles = Bouteille::all();
        return view('guest.bouteille.bouteilles', compact('bouteilles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addBouteille(Request $request, $id)
    {
        $bouteille = Bouteille::findOrfail($request->idBouteille);
        $bouteille->quantite = $bouteille->quantite + 1;
        $bouteille->save();
        return redirect()->route('celliers.show', $id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function drinkBouteille(Request $request, $id)
    {
        $bouteille = Bouteille::findOrfail($request->idBouteille);
        $bouteille->quantite = $bouteille->quantite - 1;
        $bouteille->save();

        if ($bouteille->quantite == 0) {
            $bouteille->delete();
        }

        return redirect()->route('celliers.show', $id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        $bouteilles = BouteilleSaq::all();
        $cellier = Cellier::where('id', $id)->first();
        return view('guest.bouteille.ajouterbouteille')->with('cellier', $cellier)->with('bouteilles', $bouteilles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($request->pays)) {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $fileName);
            } else {
                $fileName = 'default.jpg';
            }

            $request->validate([
                'nom' => 'required|max:100',
                'image' => ['mimes:jpeg,jpg,png', 'max:3000'],
                'date_achat' => 'required|date|before:today',
                'prix_achat' => 'required|min:0',
                'quantite' => 'required|numeric|min:1',
                'millesime' => 'required|regex:/^[1-2][0-9]{3}$/|before:' . date('Y'),
                'garde_jusqua' => 'required|date|after_or_equal:' . date('Y'),
                'description' => 'required|min:10|max:200'
            ]);

            Bouteille::create([
                'cellier_id' => $request->id,
                'nom' => $request->nom,
                'image' => $fileName,
                'pays' => $request->pays,
                'date_achat' => $request->date_achat,
                'prix_achat' => $request->prix_achat,
                'type_id' => $request->type_id,
                'quantite' => $request->quantite,
                'millesime' => $request->millesime,
                'garde_jusqua' => $request->garde_jusqua,
                'format' => $request->format,
                'description' => $request->description,
            ]);

            return redirect()->route('celliers.show', $request->id);
        } else {
            $bouteilleSAQ = BouteilleSaq::where('nom', $request->nom_SAQ)->first();

            $request->validate([
                'nom_SAQ' => 'required',
                'prix_achat_SAQ' => 'required|numeric',
                'date_achat_SAQ' => 'required|date|before:today',
                'millesime_SAQ' => 'required|regex:/^[1-2][0-9]{3}$/|before:' . date('Y'),
                'garde_jusqua_SAQ' => 'required|date|after_or_equal:' . date('Y'),
                'quantite_SAQ' => 'required|numeric|min:1'
            ]);

            Bouteille::create(
                [
                    'cellier_id' => $request->id,
                    'nom' => $bouteilleSAQ->nom,
                    'image' => $bouteilleSAQ->image,
                    'pays' => $bouteilleSAQ->pays,
                    'prix_achat' => $request->prix_achat_SAQ,
                    'millesime' => $request->millesime_SAQ,
                    'date_achat' => $request->date_achat_SAQ,
                    'garde_jusqua' => $request->garde_jusqua_SAQ,
                    'quantite' => $request->quantite_SAQ,
                    'description' => $bouteilleSAQ->description,
                    'format' => $bouteilleSAQ->format,
                    'type_id' => $bouteilleSAQ->type_id
                ]
            );

            return redirect()->route('celliers.show', $request->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $bouteille = Bouteille::findOrFail($request->idBouteille);
        $cellier = Cellier::where('id', $id)->first();
        return view('guest.bouteille.editbouteille')->with('bouteille', $bouteille)->with('cellier', $cellier);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bouteille = Bouteille::findOrfail($request->idBouteille)->first();
        $request->validate([
            'nom' => 'required',
            'pays' => 'required',
            'prix_achat' => 'required|numeric',
            'date_achat' => 'required|date',
            'garde_jusqua' => 'required|date',
            'millesime' => 'required|numeric',
            'quantite' => 'required|numeric',
            'description' => 'required',
            'format' => 'required',
        ]);

        $bouteille->update($request->all());

        return redirect()->route('celliers.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $bouteille = Bouteille::where('id', $request->idBouteille)->first();

        if (substr($bouteille->image, 0, 19) != 'https://www.saq.com') {
            $image_path = public_path('images/' . $bouteille->image);
            unlink($image_path);
        }

        $bouteille->delete();
        return redirect()->route('celliers.show', $id);
    }
}
