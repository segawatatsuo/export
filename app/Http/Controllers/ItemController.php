<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Product;
use Carbon\Carbon;

class ItemController extends Controller
{
    public function index($id)
    {
        $product = Product::where('id', $id)->first();
        return view('item', compact('product'));
    }
}
