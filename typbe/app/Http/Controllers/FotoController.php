<?php

namespace App\Http\Controllers;

use App\Foto;
use App\Pizza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoController extends Controller
{
    public function delete($id, Request $request){
        $foto = Foto::findOrFail($id);
        $pizza = Pizza::findOrFail($foto->pizza_id);
        $path = str_replace('storage/', '', $foto->path);
        Storage::disk('public')->delete($path);
        $foto->delete();
        return response()->json($pizza->load('fotos'), 201);
    }
}
