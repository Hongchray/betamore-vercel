<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index (){
        $company = Company::all();
        return response()->json([
            'success' => true,
            'message' => 'Companies retrieved successfully.',
            'data' => $company,
        ]);
    }
    public function show($companyId)
    {
        $company = Company::with('items')->find($companyId);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Company with items retrieved successfully.',
            'data' => [
                'company' => $company,
            ],
        ]);
    }
}
