<?php

namespace App\Http\Controllers;

use App\Foto;
use App\Pizza;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PizzaController extends Controller
{
    public function index(){
        $pizzas = Pizza::where('status', 'A')->orderBy('name')->get()->load('fotos');
        return response()->json($pizzas, 200);
    }

    private function loadFotos(Request $request, $pizza_id){
        $x = "";
        foreach ($request['files'] as $item => $value){
            $x = $x.'|**|'.$item;
            if(strpos($item, 'path') !== false){
                throw new \Exception('Llega');
                $imagePath = $request[$item]->store('uploads', 'public');
                $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000, 1000);
                $image->save();

                $foto = new Foto();
                $foto->pizza_id = $pizza_id;
                $foto->path = $imagePath;
                $foto->save();
            }
        }
        throw new \Exception('Request: '.$x);
    }

    public function store(Request $request){
        if($request['name'] && $request['description'] && $request['value_curr_dol'] && $request['value_curr_eur']){
            $pizza = new Pizza();
            $pizza->name = $request['name'];
            $pizza->short_desc = $request['short_desc'];
            $pizza->description = $request['description'];
            $pizza->value_curr_dol = $request['value_curr_dol'];
            $pizza->value_curr_eur = $request['value_curr_eur'];
            $pizza->status = $request['value_curr_eur'];
            if(empty($pizza->status)){
                $pizza->status = 'A';
            }
            $pizza->save();
            $this->loadFotos($request, $pizza->id);
            return response()->json($pizza->load('fotos'), 201);
        }else{
            return response()->json([ "error" => "Error creating the pizza: not all values sended" ], 501);
        }
    }

    public function show(Pizza $pizza){
        return response()->json($pizza, 200);
    }

    public function update($id, Request $request){
        if($request['name'] && $request['description'] && $request['value_curr_dol'] && $request['value_curr_eur']) {
            $pizza = Pizza::findOrFail($id);
            $pizza->name = $request['name'];
            $pizza->description = $request['description'];
            $pizza->value_curr_dol = $request['value_curr_dol'];
            $pizza->value_curr_eur = $request['value_curr_eur'];
            if(!empty($request['short_desc'])){
                $pizza->short_desc = $request['short_desc'];
            }
            if(!empty($request['status'])){
                $pizza->short_desc = $request['status'];
            }
            $pizza->save();
            $this->loadFotos($request, $id);
            return response()->json($pizza, 201);
        }else{
            return response()->json([ "error" => "Error updating the pizza: not all values sended" ], 501);
        }
    }
}
