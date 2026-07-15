<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List users with search/role filter (JSON mirror of Admin\UserController@index)
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('role')->whereHas('role', function ($q) {
            $q->where('slug', '!=', 'admin');
        });

        if ($request->has('role') && $request->role !== '') {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('role', function ($role) use ($search) {
                        $role->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->latest('created_at')->paginate(15);

        $roles = Role::where('slug', '!=', 'admin')->orderBy('title')->get();

        return response()->json([
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Show a user with their hotel information
     */
    public function show(User $user): JsonResponse
    {
        $user->load('role');

        $hotelInfo = null;

        if ($user->role->slug === 'owner') {
            $hotelInfo = $user->ownedHotels()->with('rooms')->get();
        } elseif ($user->role->slug === 'receptionist') {
            $receptionistRecord = $user->receptionistAt;
            if ($receptionistRecord) {
                $hotelInfo = $receptionistRecord->hotel()->with('rooms')->first();
            }
        } elseif (in_array($user->role->slug, ['cleaner', 'inspector'])) {
            $hotelInfo = $user->hotels()->with('rooms')->get();
        }

        return response()->json([
            'user' => $user,
            'hotelInfo' => $hotelInfo,
        ]);
    }

    /**
     * Ban a user
     */
    public function ban(User $user): JsonResponse
    {
        if ($user->role->slug === 'admin') {
            return response()->json(['message' => 'Cannot ban admin users.'], 403);
        }

        $user->update(['banned_at' => now()]);

        return response()->json(['message' => $user->name . ' has been banned successfully.']);
    }

    /**
     * Unban a user
     */
    public function unban(User $user): JsonResponse
    {
        $user->update(['banned_at' => null]);

        return response()->json(['message' => $user->name . ' has been unbanned successfully.']);
    }
}
