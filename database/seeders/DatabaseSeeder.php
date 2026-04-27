<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\HotelReceptionist;
use App\Models\HotelStaff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $roles = [
            ['title' => 'Administrator', 'slug' => 'admin'],
            ['title' => 'Hotel Owner', 'slug' => 'owner'],
            ['title' => 'Receptionist', 'slug' => 'receptionist'],
            ['title' => 'Cleaner', 'slug' => 'cleaner'],
            ['title' => 'Inspector', 'slug' => 'inspector'],
            ['title' => 'Guest', 'slug' => 'guest'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        $roleModels = Role::all()->keyBy('slug');

        // 2. Create Users for each role
        $users = [
            'admin' => [
                'name' => 'System Admin',
                'email' => 'admin@test.com',
                'password' => 'password123',
                'role_id' => $roleModels['admin']->id,
            ],
            'owner' => [
                'name' => 'Luxury Owner',
                'email' => 'owner@test.com',
                'password' => 'password123',
                'role_id' => $roleModels['owner']->id,
            ],
            'receptionist' => [
                'name' => 'Front Desk Alex',
                'email' => 'receptionist@test.com',
                'password' => 'password123',
                'role_id' => $roleModels['receptionist']->id,
            ],
            'cleaner' => [
                'name' => 'Cleaner Charlie',
                'email' => 'cleaner@test.com',
                'password' => 'password123',
                'role_id' => $roleModels['cleaner']->id,
            ],
            'inspector' => [
                'name' => 'Inspector Ian',
                'email' => 'inspector@test.com',
                'password' => 'password123',
                'role_id' => $roleModels['inspector']->id,
            ],
            'guest' => [
                'name' => 'Happy Guest',
                'email' => 'guest@test.com',
                'password' => 'password123',
                'role_id' => $roleModels['guest']->id,
            ],
        ];

        $createdUsers = [];
        foreach ($users as $key => $userData) {
            $createdUsers[$key] = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'role_id' => $userData['role_id'],
                ]
            );
        }

        // 3. Create a Luxury Hotel
        $hotel = Hotel::firstOrCreate(
            ['name' => 'Nocturnal Atelier & Spa'],
            [
                'owner_id' => $createdUsers['owner']->id,
                'description' => 'A premium luxury experience in the heart of the city, focusing on artisanal hospitality and nocturnal elegance.',
                'city' => 'Paris',
                'country' => 'France',
                'address' => '12 Rue de la Paix',
                'phone' => '+33 1 23 45 67 89',
                'email' => 'atelier@nocturnal.com',
                'rating' => 5,
                'is_verified' => true,
                'status' => 'approved',
            ]
        );

        // 4. Create Rooms with different statuses for workflow testing
        $roomTypes = ['Suite', 'Deluxe', 'Executive'];
        $statuses = ['Available', 'Occupied', 'Cleaning', 'Inspection', 'Maintenance'];
        
        foreach ($roomTypes as $index => $type) {
            for ($i = 1; $i <= 3; $i++) {
                $roomNumber = ($index + 1) * 100 + $i;
                Room::firstOrCreate(
                    ['hotel_id' => $hotel->id, 'room_number' => $roomNumber],
                    [
                        'room_type' => $type,
                        'price_per_night' => ($index + 1) * 150.00,
                        'capacity' => $index + 1,
                        'status' => $statuses[($roomNumber % count($statuses))],
                        'floor' => $index + 1,
                    ]
                );
            }
        }

        // 5. Assign Staff to the Hotel
        HotelReceptionist::firstOrCreate(
            ['user_id' => $createdUsers['receptionist']->id],
            ['hotel_id' => $hotel->id]
        );

        HotelStaff::firstOrCreate(
            ['user_id' => $createdUsers['cleaner']->id, 'hotel_id' => $hotel->id],
            ['role' => 'cleaner', 'hourly_rate' => 25.00, 'is_available' => true]
        );

        HotelStaff::firstOrCreate(
            ['user_id' => $createdUsers['inspector']->id, 'hotel_id' => $hotel->id],
            ['role' => 'inspector', 'hourly_rate' => 35.00, 'is_available' => true]
        );

        // 6. Create Coherent Workflow Bookings
        $availableRoom = Room::where('hotel_id', $hotel->id)->where('status', 'Available')->first();
        if ($availableRoom) {
            // A pending booking for the guest to test "Check-in"
            $pendingBooking = Booking::create([
                'user_id' => $createdUsers['guest']->id,
                'hotel_id' => $hotel->id,
                'check_in_date' => now()->addDays(1),
                'check_out_date' => now()->addDays(3),
                'total_amount' => $availableRoom->price_per_night * 2,
                'status' => 'pending',
                'payment_status' => 'paid',
            ]);
            
            BookingItem::create([
                'booking_id' => $pendingBooking->id,
                'room_id' => $availableRoom->id,
                'quantity' => 2,
                'price_per_night' => $availableRoom->price_per_night,
            ]);
        }

        $occupiedRoom = Room::where('hotel_id', $hotel->id)->where('status', 'Occupied')->first();
        if ($occupiedRoom) {
            // An active booking to test "Check-out"
            $activeBooking = Booking::create([
                'user_id' => $createdUsers['guest']->id,
                'hotel_id' => $hotel->id,
                'check_in_date' => now()->subDays(2),
                'check_out_date' => now(),
                'total_amount' => $occupiedRoom->price_per_night * 2,
                'status' => 'checked_in',
                'payment_status' => 'paid',
            ]);

            BookingItem::create([
                'booking_id' => $activeBooking->id,
                'room_id' => $occupiedRoom->id,
                'quantity' => 2,
                'price_per_night' => $occupiedRoom->price_per_night,
            ]);
        }
    }
}
