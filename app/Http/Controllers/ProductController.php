<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all_products() {
        return response()->json(['products' => Product::all()], 200);
    }
}
