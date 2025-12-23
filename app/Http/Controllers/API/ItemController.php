<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ItemController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $items = Item::with([
            'modifications',
            'itemPromotions.promotion',
            'images' => function ($q) {
                $q->where('is_main', 1); // ✅ Only main image
            }
        ])->get()->map(function ($item) use ($now) {

            $activePromotion = collect($item->itemPromotions)->pluck('promotion')->first(function ($promotion) use ($now) {
                if (!$promotion) return false;

                $startDate = $promotion->start_date;
                $endDate = $promotion->end_date;

                return $now->toDateString() >= $startDate->toDateString() &&
                    $now->toDateString() <= $endDate->toDateString();
            });

            $modifications = $item->modifications->map(function ($mod) use ($activePromotion) {
                $unitPrice = floatval($mod->unit_price);
                $discountAmount = 0;
                $finalPrice = $unitPrice;

                if ($activePromotion) {
                    if ($activePromotion->type === 'percent') {
                        $discountAmount = $unitPrice * floatval($activePromotion->discount_value) / 100;
                    } elseif (in_array($activePromotion->type, ['flat', 'amount'])) {
                        $discountAmount = floatval($activePromotion->discount_value);
                    }

                    $finalPrice = max(0, $unitPrice - $discountAmount);
                }

                return [
                    'id' => $mod->id,
                    'modification_name' => $mod->modification_name,
                    'unit' => $mod->unit,
                    'price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'final_price' => round($finalPrice, 2),
                ];
            });

            return [
                'id' => $item->id,
                'item_id' => $item->item_id,
                'name_en' => $item->name_en,
                'name_km' => $item->name_km,
                'description_en' => $item->description_en,
                'description_km' => $item->description_km,
                'company_id' => $item->company_id,
                'active_promotion' => $activePromotion,
                'modifications' => $modifications,
                'main_image' => $item->images->first(), // ✅ Only return one main image
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => [
                'items' => $items,
            ],
        ]);
    }
    public function show($id)
    {
        $now = Carbon::now();

        $item = Item::with('modifications', 'itemPromotions.promotion', 'images')->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.',
            ], 404);
        }

        $activePromotion = collect($item->itemPromotions)
            ->pluck('promotion')
            ->first(function ($promotion) use ($now) {
                if (!$promotion) return false;

                return $now->toDateString() >= $promotion->start_date->toDateString()
                    && $now->toDateString() <= $promotion->end_date->toDateString();
            });

        $modifications = $item->modifications->map(function ($mod) use ($activePromotion) {
            $unitPrice = floatval($mod->unit_price);
            $discountAmount = 0;
            $finalPrice = $unitPrice;

            if ($activePromotion) {
                if ($activePromotion->type === 'percent') {
                    $discountAmount = $unitPrice * floatval($activePromotion->discount_value) / 100;
                } elseif (in_array($activePromotion->type, ['flat', 'amount'])) {
                    $discountAmount = floatval($activePromotion->discount_value);
                }

                $finalPrice = max(0, $unitPrice - $discountAmount);
            }

            return [
                'id' => $mod->id,
                'modification_name' => $mod->modification_name,
                'unit' => $mod->unit,
                'price' => $unitPrice,
                'discount_amount' => $discountAmount,
                'final_price' => round($finalPrice, 2),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => [
                'id' => $item->id,
                'item_id' => $item->item_id,
                'name_en' => $item->name_en,
                'name_km' => $item->name_km,
                'description_en' => $item->description_en,
                'description_km' => $item->description_km,
                'company_id' => $item->company_id,
                'active_promotion' => $activePromotion,
                'modifications' => $modifications,
                'images' => $item->images,
            ],
        ]);
    }


}
