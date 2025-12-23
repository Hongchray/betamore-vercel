<?php

namespace App\Http\Controllers\API;
use App\Models\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BannerController extends Controller
{
     public function index()
    {
        $banners = Banner::all();

        return response()->json([
            'success' => true,
            'message' => 'Banners retrieved successfully.',
            'data' => $banners,
        ]);
    }
}
