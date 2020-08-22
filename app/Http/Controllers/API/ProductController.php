<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::where('qty_stock', '>', 0)
               ->get();

        return response()->json(['data' => $products], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'qty_stock' => $request->qty_stock,

        ]);

        return response()->json(['id' => $product->id], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product = Product::find($product->id);
        $last_purchase = Product::find($product->id)->purchases()->orderBy('purchase_date', 'DESC')->first();

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'amount' => $product->amount,
            'qty_stock' => $product->qty_stock,
            'last_purchase' => $last_purchase
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product = Product::find($product->id);
        if(empty($product))
        {
            return response()->json(['Erro nos dados enviados'], 400);
        }
        $product->delete();
        return response()->json(['Produto excluído com sucesso'], 200);
    }
}
