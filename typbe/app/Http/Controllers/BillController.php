<?php

namespace App\Http\Controllers;

use App\Bill;
use App\PizzaBill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    private function calculate($bills){
        foreach($bills as $bill){
            $bill->value_ord = 0;
            foreach ($bill->pizzas as $pizza){
                $pizza->load('pizza');
                $bill->value_ord = $bill->value_ord + ($pizza->uni_value * $pizza->quantity);
            }
            $bill->value = $bill->value_ord + $bill->value_del;
        }
        return $bills;
    }

    public function index(){
        return response()->json($this->calculate(Bill::all()->sortByDesc('created_at')->load('user')), 200);
    }

    private function totalize(Bill $bill){
        foreach ($bill->pizzas as $pizza){
            $pizza->uni_value = 0;
            if($bill->currency == 'D'){
                $pizza->uni_value = $pizza->pizza->value_curr_dol;
            }else{
                $pizza->uni_value = $pizza->pizza->value_curr_eur;
            }
            $pizza->save();
        }
        $bills = $this->calculate([$bill]);
        return $bills[0];
    }

    public function store(Request $request){
        if($request['pizza_id'] && $request['quantity']){
            $bill = new Bill();
            if(!empty($request['user_id'])) {
                $bill->user_id = $request["user_id"];
            }
            $bill->currency = $request["currency"];
            $bill->value_del = 0;
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
            $pizzaBill->save();
            $bill = $this->totalize($bill);
            return response()->json($bill->load('user'), 201);
        }else{
            return response()->json([ "error" => "Error creating the bill: not all values sended" ], 501);
        }
    }

    public function show(Bill $bill){
        $bills = $this->calculate([$bill->load('user')]);
        return response()->json($bills[0], 200);
    }

    public function update($id, Request $request){
        $bill = Bill::findOrFail($id)->load('user');
        if(!empty($request['user_id'])){
            $bill->user_id = $request['user_id'];
        }
        if(!empty($request['currency'])){
            $bill->currency = $request['currency'];
        }
        if(!empty($request['value_del'])){
            $bill->value_del = $request['value_del'];
        }
        if(!empty($request['status'])){
            $bill->status = $request['status'];
        }
        if(!empty($request['location'])){
            $bill->location = $request['location'];
        }
        if(!empty($request['mobile'])){
            $bill->mobile = $request['mobile'];
        }
        if(!empty($request['additional_dat'])){
            $bill->additional_dat = $request['additional_dat'];
        }
        $bill->save();
        if($request['pizza_id'] && $request['quantity']) {
            $pizzaBill = new PizzaBill();
            $pizzaBill->pizza_id = $request['pizza_id'];
            $pizzaBill->bill_id = $id;
            $pizzaBill->quantity = $request['quantity'];
            $pizzaBill->uni_value = 0;
            $pizzaBill->save();
        }
        $bill = $this->totalize($bill);
        return response()->json($bill, 201);
    }
}
