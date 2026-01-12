<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Prescription for') }} <span class="text-blue-600">{{ $prescription->examination->patient->name }}</span>
            </h2>
            <a href="{{ route('doctor.examinations') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-md text-sm font-bold hover:bg-gray-200 transition">
                &larr; BACK TO DASHBOARD
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- REFERENSI CATATAN KLINIS --}}
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider">Clinical Notes Reference:</h3>
                        <div class="mt-1 text-sm text-blue-700 italic">
                            "{{ $prescription->examination->notes ?: 'No clinical notes provided.' }}"
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- LEFT SIDE: ADD MEDICINE FORM --}}
                <div class="md:col-span-1">
                    <div class="bg-white p-6 shadow-md rounded-lg border-t-4 border-red-600">
                        <h3 class="text-md font-bold text-gray-800 mb-4 uppercase flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Add Medicine
                        </h3>
                        
                        <form action="{{ route('doctor.prescriptions.items.add', $prescription->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Select Medicine</label>
                                <select name="medicine_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                    <option value="">-- Choose Medicine --</option>
                                    @if(isset($medicines))
                                        @foreach($medicines as $med)
                                            <option value="{{ $med['id'] }}">{{ $med['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Quantity</label>
                                <input type="number" name="quantity" min="1" value="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-md border-b-4 border-blue-800 active:border-b-0">
                                Add to List
                            </button>
                        </form>
                    </div>
                </div>

                {{-- RIGHT SIDE: PRESCRIPTION LIST --}}
                <div class="md:col-span-2">
                    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-blue-600">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 uppercase text-sm tracking-widest">Prescription Items</h3>
                            <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">ID: #{{ $prescription->id }}</span>
                        </div>

                        <table class="w-full text-sm text-left">
                            <thead class="bg-blue-600 text-white uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="px-6 py-3">Medicine Name</th>
                                    <th class="px-6 py-3 text-center">Qty</th>
                                    <th class="px-6 py-3 text-right">Unit Price</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($prescription->items as $item)
                                    <tr class="hover:bg-blue-50/30 transition duration-150">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-blue-900">{{ $item->medicine_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-semibold text-gray-700">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-right text-gray-500">Rp{{ number_format($item->unit_price) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900">Rp{{ number_format($item->total_price) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('doctor.prescriptions.items.remove', $item->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic bg-gray-50/50">
                                            No medicines added to this prescription yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            @if($prescription->items->count() > 0)
                            <tfoot class="bg-blue-50/50 border-t-2 border-blue-100">
                                <tr>
                                    <th colspan="3" class="px-6 py-4 text-right uppercase text-[10px] font-black text-blue-800 tracking-tighter">Estimated Total Cost</th>
                                    <th class="px-6 py-4 text-right text-lg font-black text-blue-600">
                                        Rp{{ number_format($prescription->items->sum('total_price')) }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    @if($prescription->items->count() > 0)
                        <div class="mt-6 flex justify-end">
                            <form action="{{ route('doctor.prescriptions.finish', $prescription->id) }}" method="POST" onsubmit="return">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-8 py-3 rounded-lg font-black text-sm uppercase tracking-widest shadow-lg hover:bg-red-700 transition transform hover:scale-105 border-b-4 border-red-900 active:border-b-0 active:scale-95 flex items-center">
                                    Finish & Send to Pharmacy
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div> 
            </div>
        </div> 
    </div> 
</x-app-layout>