<?php

namespace App\Http\Controllers;

use App\Foto;
use App\Pizza;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PizzaController extends Controller
{
    public function index(){
        return response()->json(Pizza::where('status', 'A')->orderBy('name'), 201);
    }

    private function prepareRecord(Request $request){
        $pizza = new Pizza();
        $pizza->name = $request['name'];
        $pizza->short_desc = $request['short_desc'];
        $pizza->description = $request['description'];
        $pizza->value_curr_dol = $request['value_curr_dol'];
        $pizza->value_curr_eur = $request['value_curr_eur'];
        $pizza->status = 'A';
        return $pizza;
    }

    private function loadFotos(Request $request, $pizza_id){
        foreach ($request as $item => $value){
            if(strpos($item, 'path') !== false){
                $imagePath = $request[$item]->store('uploads', 'public');
                $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000, 1000);
                $image->save();

                $foto = new Foto();
                $foto->pizza_id = $pizza_id;
                $foto->path = $imagePath;
                $foto->save();
            }
        }
    }

    public function store(Request $request){
        if($request['name'] && $request['description'] && $request['value_curr_dol'] && $request['value_curr_eur']){
            $pizza = $this->prepareRecord($request);
            $pizza->save();
            $this->loadFotos($request, $pizza->id);
            return response()->json($pizza, 201);
        }else{
            return response()->json([ "error" => "Error creating the pizza: not all values sended" ], 501);
        }
    }

    public function show(Pizza $pizza){
        return response()->json($pizza, 201);
    }

    public function update(Request $request, $id){
        if($request['name'] && $request['description'] && $request['value_curr_dol'] && $request['value_curr_eur']) {
            $pizza = $this->prepareRecord($request);
            $pizza->id = $id;
            $pizza->status = $request['status'];
            $pizza->update();
            $this->loadFotos($request, $id);
            return response()->json($pizza, 201);
        }else{
            return response()->json([ "error" => "Error updating the pizza: not all values sended" ], 501);
        }
    }
}