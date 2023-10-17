<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except'=>['login','register']]);   
    }

    //
    public function index() {
        $data = Transaction::select('transactions.*', 'pro.name_product', 'user.name_user', 'cust.name_customer')
                            ->LeftJoin('users as user', 'transactions.id_user', '=', 'user.id_user')
                            ->LeftJoin('customers as cust', 'transactions.id_customer', '=', 'cust.id_customer')
                            ->LeftJoin('products as pro', 'transactions.id_product', '=', 'pro.id_product')
                            ->get();

        if (count($data) > 0) {
            return response()->json($data, 200);
        } else {    
            return response()->json(["error" => "Data kosong"], 400);
        } 
    }

    public function show($id) {
        $data = Transaction::select('transactions.*', 'pro.name_product', 'user.name_user', 'cust.name_customer')
                ->LeftJoin('users as user', 'transactions.id_user', '=', 'user.id_user')
                ->LeftJoin('customers as cust', 'transactions.id_customer', '=', 'cust.id_customer')
                ->LeftJoin('products as pro', 'transactions.id_product', '=', 'pro.id_product')
                ->where('id_transaction', $id)
                ->first();

        if ($data) {
            return response()->json($data, 200);
        } else {    
            return response()->json(["error" => "Data kosong"], 400);
        } 
    }

    public function store(Request $request){
        try {

            $validate = $request->validate([
                'id_user'         => 'required|numeric',
                'id_customer'     => 'required|numeric',
                'id_product'      => 'required|numeric',
                'quantity'        => 'required|numeric',
                'total_payment'   => 'required|numeric',
                'money_paid'      => 'required|numeric',
            ],[
                'required'  => "Data tidak boleh kosong",
                'numeric'   => "Data harus bersifat number"
            ]);  
            
            $data = Transaction::create($validate);

            return response()->json([
                'message'=> 'Transaction suceessfully insert',
                'data'  => $data
            ], 201) ;
        } catch (\Exception $e) {
            // If an exception occurs, check if it's a validation exception
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json($e->errors(), 400);
            }
        
            // Handle other exceptions if needed
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
}
