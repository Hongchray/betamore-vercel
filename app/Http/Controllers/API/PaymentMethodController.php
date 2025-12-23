<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::all();

        return response()->json([
            'message' => 'Payment methods retrieved successfully.',
            'data' => $methods
        ], 200);
    }
}
