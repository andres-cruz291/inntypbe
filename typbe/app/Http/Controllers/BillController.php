<?php

namespace App\Http\Controllers;

use App\Bill;
use App\PizzaBill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    private function calculate($bills){
        foreach($bills as $bill){
            foreach ($bill->pizzas as $pizza){
                $pizza->load('pizza');
                $bill->value_ord = $bill->value_ord + ($pizza->uni_value * $pizza->quantity);
            }
            $bill->value = $bill->value_ord + $bill->value_del;
        }
        return $bills;
    }

    public function index(){
        return response()->json($this->calculate(Bill::all()->sortByDesc('created_at')), 201);
    }

    private function totalize(Bill $bill){
        foreach ($bill->pizzas as $pizza){
            $pizzaD["uni_value"] = 0;
            if($bill->currency == 'D'){
                $pizzaD["uni_value"] = $pizza->pizza->value_curr_dol;
            }else{
                $pizzaD["uni_value"] = $pizza->pizza->value_curr_eur;
            }
            $pizza->update($pizzaD, $pizza->id);
        }
        $bills = $this->calculate([$bill]);
        return $bills[0];
    }

    public function store(Request $request){
        if($request['pizza_id'] && $request['quantity']){
            $bill = new Bill();
            $bill->user_id = $request["bill_id"];
            $bill->user_id = $request["user_id"];
            $bill->currency = $request["currency"];
            $bill->value_ord = 0;
            $bill->value_del = 0;
            $bill->value_bil = 0;
            $bill->status = 'P';
            if(empty($bill->currency)){
                $bill->currency = 'E';
            }
            $bill->save();
            $pizzaBill = new PizzaBill();
            $pizzaBill->pizza_id = $request['pizza_id'];
            $pizzaBill->bill_id = $bill->id;
            $pizzaBill->quantity = $request['quantity'];
            $pizzaBill->uni_value = 0;
            $pizzaBill->tot_value = 0;
            $pizzaBill->save();
            $bill = $this->totalize($bill);
            return response()->json($bill, 201);
        }else{
            return response()->json([ "error" => "Error creating the pizza: not all values sended" ], 501);
        }
    }

    public function show(Bill $bill){
        return response()->json($bill, 201);
    }

    public function update(Request $request, $id){
        $bill = Bill::findOrFail($id);
        $bill->update($request);
        if($request['pizza_id'] && $request['quantity']) {
            $pizzaBill = new PizzaBill();
            $pizzaBill->pizza_id = $request['pizza_id'];
            $pizzaBill->bill_id = $request['pizza_id'];
            $pizzaBill->quantity = $request['quantity'];
            $pizzaBill->uni_value = 0;
            $pizzaBill->tot_value = 0;
            $pizzaBill->save();
        }
        $bill = $this->totalize($bill);
        return response()->json($bill, 201);
    }
}
