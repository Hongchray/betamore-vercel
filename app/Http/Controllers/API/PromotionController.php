<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use AWS\CRT\HTTP\Response;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(){
        $promotion = Promotion::with('items')->get();
        return response()->json([
            'message' => 'Promotion retrieved successfully.',
            'data' => $promotion
        ]);
    }
    public function show($id)
    {
        $promotion = Promotion::with('items')->find($id);

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion not found.',
            ], 404); // or 200 if you don't want HTTP error status
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion retrieved successfully.',
            'data' => $promotion
        ]);
    }

}
