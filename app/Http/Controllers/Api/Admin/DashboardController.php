<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Admin dashboard statistics (JSON mirror of Admin\DashboardController@index)
     */
    public function index(): JsonResponse
    {
        $stats = [
            'totalHotels' => Hotel::count(),
            'totalUsers' => User::count(),
            'totalBookings' => Booking::count(),
            'totalRevenue' => $this->calculateRevenue(),
            'pendingRequests' => HotelRequest::where('status', 'pending')->count(),
            'approvedRequests' => HotelRequest::where('status', 'approved')->count(),
            'rejectedRequests' => HotelRequest::where('status', 'rejected')->count(),
        ];

        $systemUsers = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('slug', '!=', 'admin');
            })
            ->latest('created_at')
            ->limit(10)
            ->get();

        $recentRequests = HotelRequest::with('owner', 'reviewer')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'systemUsers' => $systemUsers,
            'recentRequests' => $recentRequests,
        ]);
    }

    private function calculateRevenue(): float
    {
        return Booking::where('status', '!=', 'cancelled')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->sum('total_amount') ?? 0;
    }
}
