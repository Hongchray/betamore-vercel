<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DeliveryController extends Controller
{
    public function index()
    {
        $delivery = Delivery::where('is_active', '=', DB::raw('true'))->get();

        return response()->json([
            'message' => $delivery->isEmpty() 
                ? 'No active deliveries available.'
                : 'Delivery retrieved successfully.',
            'data' => $delivery
        ], 200);
    }

}
