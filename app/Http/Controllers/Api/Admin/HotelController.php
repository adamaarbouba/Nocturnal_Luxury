<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    /**
     * Show a hotel with rooms and owner (JSON mirror of Admin\HotelController@show)
     */
    public function show(Hotel $hotel): JsonResponse
    {
        $hotel->load('rooms', 'owner');

        return response()->json(['hotel' => $hotel]);
    }
}
