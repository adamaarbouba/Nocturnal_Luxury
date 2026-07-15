@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <x-breadcrumbs :links="[
            ['label' => 'Owner Dashboard', 'url' => route('owner.dashboard')],
            ['label' => 'Manage Hotel', 'url' => '#']
        ]" />

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-[#EAD3CD]">{{ $hotel->name }} - Management</h2>
            <a href="{{ route('owner.hotels.index') }}"
                class="border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold px-6 py-2 rounded transition">
                Back to Hotels
            </a>
        </div>

        <!-- Room Management Section -->
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-[#EAD3CD]">Room Management</h3>
                <button onclick="openAddRoomModal()"
                    class="bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition">
                    Add New Room
                </button>
            </div>

            @if ($hotel->rooms->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#2A2729] border-b border-[#4E3B46]">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Room Number</th>
                                <th class="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Type</th>
                                <th class="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Capacity</th>
                                <th class="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Price</th>
                                <th class="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hotel->rooms as $room)
                                <tr class="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                                    <td class="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">
                                        {{ $room->room_number }}</td>
                                    <td class="px-6 py-4 text-sm text-[#CFCBCA]">{{ ucfirst($room->room_type) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#CFCBCA]">{{ $room->capacity }} guests</td>
                                    <td class="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">
                                        ${{ number_format($room->price_per_night, 2) }}</td>
                                    <td class="px-6 py-4 text-sm flex gap-3">
                                        @if ($room->status === 'Occupied')
                                            <button disabled class="text-[#4E3B46] cursor-not-allowed font-semibold"
                                                title="Cannot edit occupied room">
                                                Edit
                                            </button>
                                            <button disabled class="text-[#4E3B46] cursor-not-allowed font-semibold"
                                                title="Cannot disable occupied room">
                                                Disable
                                            </button>
                                        @elseif ($room->status === 'Disabled')
                                            <button disabled class="text-[#4E3B46] cursor-not-allowed font-semibold"
                                                title="Room is disabled">
                                                Edit
                                            </button>
                                            <button disabled class="text-[#4E3B46] cursor-not-allowed font-semibold"
                                                title="Room is already disabled">
                                                Disable
                                            </button>
                                        @else
                                            <button
                                                onclick="editRoom({{ $room->id }}, '{{ $room->room_number }}', '{{ $room->room_type }}', {{ $room->capacity }}, {{ $room->price_per_night }})"
                                                class="text-[#A0717F] hover:text-[#EAD3CD] font-semibold transition">
                                                Edit
                                            </button>
                                            <button onclick="deleteRoom({{ $room->id }}, '{{ $room->room_number }}')"
                                                class="text-[#CFCBCA] hover:text-red-500 font-semibold transition">
                                                Disable
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 border-t border-[#4E3B46]">
                    <p class="text-[#CFCBCA] text-lg mb-4">No rooms added yet</p>
                    <button onclick="openAddRoomModal()"
                        class="inline-block bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition">
                        Add First Room
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Room Modal -->
    <div id="roomModal" class="fixed inset-0 bg-[#1A1515] bg-opacity-80 hidden z-50 flex items-center justify-center">
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-xl p-8 w-full max-w-md mx-4">
            <h3 id="modalTitle" class="text-2xl font-bold text-[#EAD3CD] mb-6">Add New Room</h3>

            <form id="roomForm" onsubmit="submitRoomForm(event)">
                @csrf
                <div class="mb-4">
                    <label class="block text-[#CFCBCA] font-semibold mb-2">Room Number</label>
                    <input type="text" id="roomNumber" name="room_number" required
                        class="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
                        placeholder="e.g., 101, 102">
                </div>

                <div class="mb-4">
                    <label class="block text-[#CFCBCA] font-semibold mb-2">Room Type</label>
                    <select id="roomType" name="type" required
                        class="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]">
                        <option value="">Select Type</option>
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="suite">Suite</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-[#CFCBCA] font-semibold mb-2">Capacity (Guests)</label>
                    <input type="number" id="roomCapacity" name="capacity" required min="1"
                        class="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
                        placeholder="e.g., 2">
                </div>

                <div class="mb-6">
                    <label class="block text-[#CFCBCA] font-semibold mb-2">Price per Night</label>
                    <input type="number" id="roomPrice" name="price" required min="0" step="0.01"
                        class="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
                        placeholder="e.g., 99.99">
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold py-2 rounded transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold py-2 rounded transition">
                        Save Room
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const hotelId = {{ $hotel->id }};

        function openAddRoomModal() {
            document.getElementById('modalTitle').textContent = 'Add New Room';
            document.getElementById('roomForm').reset();
            document.getElementById('roomForm').dataset.roomId = '';
            document.getElementById('roomModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('roomModal').classList.add('hidden');
        }

        function submitRoomForm(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('roomForm'));
            const roomId = document.getElementById('roomForm').dataset.roomId;
            const data = {
                room_number: formData.get('room_number'),
                type: formData.get('type'),
                capacity: formData.get('capacity'),
                price: formData.get('price'),
                _token: document.querySelector('input[name="_token"]').value,
            };

            let url, method;
            if (roomId) {
                // Edit mode
                url = `/owner/hotels/${hotelId}/rooms/${roomId}/update`;
                method = 'POST';
            } else {
                // Add mode
                url = `/owner/hotels/${hotelId}/add-room`;
                method = 'POST';
            }

            fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': data._token,
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        NocturnalUI.alert(result.message, 'Success').then(() => location.reload());
                    } else {
                        NocturnalUI.alert('Error: ' + (result.message || 'Something went wrong'), 'Update Failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    NocturnalUI.alert('Error processing request', 'System Error');
                });
        }

        function updateRoomStatus(roomId, status) {
            const data = {
                room_id: roomId,
                status: status,
                _token: document.querySelector('input[name="_token"]').value,
            };

            fetch(`/owner/hotels/${hotelId}/update-room-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': data._token,
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Reload to reflect status change with updated colors
                        location.reload();
                    } else {
                        NocturnalUI.alert('Error updating room status', 'Update Failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    NocturnalUI.alert('Error updating room status', 'System Error');
                });
        }

        function editRoom(roomId, roomNumber, roomType, capacity, price) {
            document.getElementById('modalTitle').textContent = 'Edit Room';
            document.getElementById('roomNumber').value = roomNumber;
            document.getElementById('roomType').value = roomType;
            document.getElementById('roomCapacity').value = capacity;
            document.getElementById('roomPrice').value = price;
            document.getElementById('roomForm').dataset.roomId = roomId;
            document.getElementById('roomModal').classList.remove('hidden');
        }

        function deleteRoom(roomId, roomNumber) {
            NocturnalUI.confirm(`Are you sure you want to disable room ${roomNumber}? The room will be marked as disabled and cannot be used for bookings.`, 'Disable Room').then((isConfirmed) => {
                if (isConfirmed) {
                    const token = document.querySelector('input[name="_token"]').value;

                    fetch(`/owner/hotels/${hotelId}/rooms/${roomId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                NocturnalUI.alert(result.message, 'Success').then(() => location.reload());
                            } else {
                                NocturnalUI.alert('Error: ' + (result.message || 'Something went wrong'), 'Deletion Failed');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            NocturnalUI.alert('Error disabling room', 'System Error');
                        });
                }
            });
        }

        // Close modal when clicking outside
        document.getElementById('roomModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
    </script>
@endsection




