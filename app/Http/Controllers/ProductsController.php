<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except'=>['login','register']]);   
    }
    
    //
    public function index() {
        $data = Product::LeftJoin('categories as cat', 'products.id_category', '=', 'cat.id_category')->get();

        if (count($data) > 0) {
            return response()->json($data, 200);
        } else {    
            return response()->json(["error" => "Data kosong"], 400);
        } 
    }

    public function show($id) {
        $data = Product::LeftJoin('categories as cat', 'products.id_category', '=', 'cat.id_category')
                ->where('id_product', $id)
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
                'id_category'     => 'required',
                'name_product'    => 'required',
                'price'           => 'required|numeric',
                'stock'           => 'required|numeric',
            ],[
                'required'  => "Data tidak boleh kosong",
                'numeric'   => "Data harus bersifat number"
            ]);  
            
            $data = Product::create($validate);

            return response()->json([
                'message'=> 'Product suceessfully insert',
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
