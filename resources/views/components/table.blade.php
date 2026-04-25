<div class="overflow-hidden rounded-lg border border-[#4E3B46] bg-[#383537] shadow-sm">
    <table class="w-full">
        <thead class="border-b border-[#4E3B46] bg-[#2A2729]">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-6 py-4 text-left text-sm font-semibold text-[#EAD3CD]">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-[#4E3B46]">
            {{ $slot }}
        </tbody>
    </table>
</div>


@php
    if (!isset($skipRowComponent)) {
        $skipRowComponent = false;
    }
@endphp




