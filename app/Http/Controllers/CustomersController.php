<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomersController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except'=>['login','register']]);   
    }
    
    public function index() {
        $data = Customer::all();

        if (count($data) > 0) {
            return response()->json($data, 200);
        } else {    
            return response()->json(["error" => "Data kosong"], 400);
        } 
    }

    public function show($id) {
        $data = Customer::find($id);

        if ($data) {
            return response()->json($data, 200);
        } else {    
            return response()->json(["error" => "Data kosong"], 400);
        } 
    }

    public function store(Request $request){
        try {

            $validate = $request->validate([
                'name_customer'     => 'required',
                'email'             => 'required|email',
                'address'           => 'required',
                'gender'            => 'required',
                'number_phone'      => 'required',
                'birthdate'         => 'required|date',
            ],[
                'required'  => "Data tidak boleh kosong",
                'date' => "Data harus tanggal",
                'email' => "Data harus bersifat email"
            ]);  
            
            $data = Customer::create($validate);

            return response()->json([
                'message'=> 'Customer suceessfully insert',
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
