<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="flex items-center p-4 bg-green-50 rounded-lg border-l-4 border-green-500 shadow-sm animate-fade-in-down">
                    <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Clinical Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-5 bg-white shadow-sm rounded-xl border border-gray-100 border-t-4 border-blue-600 transition-transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-50 rounded-xl mr-4 group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em]">Pending Validation</p>
                            <p class="text-2xl font-black text-blue-900 leading-tight">{{ $prescriptions->where('status', 'pending')->count() }} <span class="text-sm font-medium text-gray-500">Cases</span></p>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-white shadow-sm rounded-xl border border-gray-100 border-t-4 border-orange-500 transition-transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-50 rounded-xl mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em]">Awaiting Settlement</p>
                            <p class="text-2xl font-black text-orange-900 leading-tight">{{ $prescriptions->where('status', 'calculated')->count() }} <span class="text-sm font-medium text-gray-500">Ready</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prescription Queue --}}
            <div class="bg-white shadow-md rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-white flex justify-between items-center">
                    <h3 class="text-base font-black text-blue-900 uppercase tracking-widest flex items-center">
                        <span class="w-1.5 h-5 bg-red-600 mr-3 rounded-full"></span>
                        Pharmacy Dispensing Queue
                    </h3>
                    <button onclick="window.location.reload()" class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-400" title="Refresh Queue">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>

                <div class="relative overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky top-0 z-10 text-[11px] text-white uppercase bg-blue-700 shadow-md">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Patient No. RM</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Full Name</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Clinical Summary</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-center">Action</th>
                            </tr>
                        </thead>
                        
                        <tbody class="divide-y divide-gray-100" x-data="{ selectedRow: null }">
                            @forelse ($prescriptions as $p)
                                <tr class="transition-colors" 
                                    :class="selectedRow === {{ $p->id }} ? 'bg-blue-50/50' : 'bg-white hover:bg-gray-50'">
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-black text-blue-600 tracking-tighter">
                                            {{ $p->examination->patient_id }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                                            {{ $p->created_at->format('d M Y • H:i') }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 font-extrabold text-gray-800 uppercase tracking-tight">
                                        {{ $p->examination->patient->name }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <button @click="selectedRow === {{ $p->id }} ? selectedRow = null : selectedRow = {{ $p->id }}" 
                                                class="group flex flex-col items-start focus:outline-none">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[9px] font-black rounded border border-blue-200 uppercase">
                                                    {{ $p->items->count() }} Meds
                                                </span>
                                                <span class="text-[11px] font-black text-gray-900 tracking-tighter">
                                                    IDR {{ number_format($p->items->sum('total_price'), 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="flex items-center text-[10px] text-gray-400 italic font-bold group-hover:text-blue-600 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" :class="selectedRow === {{ $p->id }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                                </svg>
                                                Review Prescriptions
                                            </div>
                                        </button>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @if($p->status === 'paid')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black bg-green-50 text-green-700 border border-green-200 uppercase tracking-tighter">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Dispensed
                                            </span>
                                        @elseif($p->status === 'calculated')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black bg-orange-50 text-orange-700 border border-orange-200 uppercase tracking-tighter">
                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5"></span> Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-tighter animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5"></span> Waiting
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center text-[0]">
                                        <div class="inline-flex shadow-sm rounded-md overflow-hidden">
                                            @if($p->status === 'pending')
                                                <form action="{{ route('pharmacist.prescriptions.calculate', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 font-black text-[10px] text-white uppercase hover:bg-blue-700 transition">
                                                        Validate
                                                    </button>
                                                </form>
                                            @elseif($p->status === 'calculated')
                                                <form action="{{ route('pharmacist.prescriptions.pay', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Confirm payment and lock medical record?')" 
                                                            class="px-4 py-2 bg-orange-500 font-black text-[10px] text-white uppercase hover:bg-orange-600 transition">
                                                        Process Payment
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('pharmacist.prescriptions.print', $p->id) }}" target="_blank" 
                                                   class="px-4 py-2 bg-gray-50 border border-gray-200 font-black text-[10px] text-gray-700 uppercase hover:bg-gray-100 transition flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg> Print
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                
                                {{-- Itemization Drawer --}}
                                <tr x-show="selectedRow === {{ $p->id }}" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2">
                                    <td colspan="5" class="px-8 py-4 bg-gray-50/80">
                                        <div class="bg-white border border-blue-100 rounded-lg shadow-inner overflow-hidden">
                                            <table class="w-full text-[11px] text-left">
                                                <thead class="bg-gray-50 border-b border-gray-100 text-gray-400 font-bold uppercase tracking-widest">
                                                    <tr>
                                                        <th class="px-4 py-3">Pharmacological Name</th>
                                                        <th class="px-4 py-3 text-center">Qty</th>
                                                        <th class="px-4 py-3">Unit Price</th>
                                                        <th class="px-4 py-3 text-right">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50">
                                                    @foreach($p->items as $item)
                                                        <tr class="hover:bg-blue-50/30">
                                                            <td class="px-4 py-2.5 font-bold text-gray-700 underline decoration-blue-100">{{ $item->medicine_name }}</td>
                                                            <td class="px-4 py-2.5 text-center font-bold">{{ $item->quantity }}</td>
                                                            <td class="px-4 py-2.5 text-gray-500">IDR {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                            <td class="px-4 py-2.5 text-right font-black text-blue-900">IDR {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-12 w-12 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-xs font-black text-gray-300 uppercase tracking-[0.2em]">Zero active clinical requests</span>
                                            <button onclick="window.location.reload()" class="mt-4 text-xs font-bold text-blue-600 hover:underline">Refresh Page</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>