<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="font-medium text-sm text-green-600 p-4 bg-green-100 rounded-lg shadow-sm border-l-4 border-green-500">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Menampilkan error validasi jika ada --}}
            @if ($errors->any())
                <div class="font-medium text-sm text-red-600 p-4 bg-red-100 rounded-lg shadow-sm border-l-4 border-red-500">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            {{-- FORM EXAMINATION --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-t-4 border-blue-600">
                <h3 class="text-lg font-bold text-blue-900 mb-4 uppercase tracking-wider flex items-center">
                    <span class="w-2 h-6 bg-red-600 mr-2"></span>
                    New Patient Examination
                </h3>
                
                <form action="{{ route('doctor.examinations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="patient_id" :value="__('Select Patient (Medical Record)')" />
                            {{-- PERUBAHAN DISINI: Menggunakan Select agar dokter tinggal memilih --}}
                            <select id="patient_id" name="patient_id" class="mt-1 block w-full bg-gray-50 border-blue-200 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm" required>
                                <option value="" disabled selected>-- Choose Registered Patient --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        [{{ $patient->id }}] - {{ $patient->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="examined_at" :value="__('Examination Time')" />
                            <x-text-input id="examined_at" name="examined_at" type="datetime-local" class="mt-1 block w-full border-blue-200 focus:border-blue-500 focus:ring-blue-500" value="{{ now()->format('Y-m-d\TH:i') }}" required />
                        </div>
                    </div>

                    {{-- VITAL SIGNS BOX --}}
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <h4 class="text-xs font-bold text-blue-700 uppercase mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Vital Signs
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-blue-800 uppercase">Height (cm)</label>
                                <input name="height" type="number" step="0.1" value="{{ old('height') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-800 uppercase">Weight (kg)</label>
                                <input name="weight" type="number" step="0.1" value="{{ old('weight') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-800 uppercase">BP (Sys/Dia)</label>
                                <div class="flex items-center space-x-1">
                                    <input name="systole" type="number" required placeholder="120" value="{{ old('systole') }}" class="w-full rounded-md border-gray-300 shadow-sm text-xs px-1">
                                    <span class="text-gray-400">/</span>
                                    <input name="diastole" type="number" required placeholder="80" value="{{ old('diastole') }}" class="w-full rounded-md border-gray-300 shadow-sm text-xs px-1">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-800 uppercase">Pulse</label>
                                <input name="heart_rate" type="number" value="{{ old('heart_rate') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-800 uppercase">Resp. Rate</label>
                                <input name="respiration_rate" type="number" value="{{ old('respiration_rate') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-800 uppercase">Temp (°C)</label>
                                <input name="temperature" type="number" step="0.1" value="{{ old('temperature') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Clinical Examination Results')" class="text-blue-900 font-bold" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-blue-200 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm placeholder-gray-400 text-sm" placeholder="Write clinical observations...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <x-input-label for="attachment" :value="__('Upload External Medical Records')" />
                        <input type="file" name="attachment" id="attachment" class="mt-2 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-md transition">
                            {{ __('Save Examination') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLE HISTORY --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Examination History
                    </h3>
                    <div class="relative overflow-x-auto shadow-sm sm:rounded-lg border border-gray-100">
                        <table class="w-full text-sm text-gray-500">
                            <thead class="text-xs text-white uppercase bg-blue-600">
                                <tr>
                                    <th class="px-6 py-3 text-left">Date & Time</th>
                                    <th class="px-6 py-3 text-left">Patient Details</th>
                                    <th class="px-6 py-3 text-left">Clinical Notes</th>
                                    <th class="px-6 py-3 text-center">Attachment</th>
                                    <th class="px-6 py-3 text-center">Prescription Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($examinations as $exam)
                                    <tr class="bg-white border-b hover:bg-blue-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-left">
                                            <div class="text-sm font-bold text-gray-900">{{ $exam->created_at->format('d M Y') }}</div>
                                            <div class="text-[10px] text-blue-500 font-medium">{{ $exam->created_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="px-6 py-4 text-left">
                                            <div class="text-sm font-bold text-blue-900">{{ $exam->patient->name }}</div>
                                            <div class="text-[10px] text-gray-500 uppercase tracking-tighter">NO. RM: {{ $exam->patient_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-left">
                                            <div class="text-xs text-gray-600 line-clamp-2 max-w-[250px]" title="{{ $exam->notes }}">
                                                {{ $exam->notes ?: '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($exam->attachment_path)
                                                <a href="{{ asset('storage/' . $exam->attachment_path) }}" target="_blank" 
                                                class="inline-flex items-center px-3 py-1 bg-white border border-blue-600 text-blue-600 rounded text-[10px] font-bold hover:bg-blue-600 hover:text-white transition">
                                                    VIEW FILE
                                                </a>
                                            @else
                                                <span class="text-[10px] text-gray-400 uppercase">None</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                @if($exam->prescription)
                                                    @php
                                                        $status = $exam->prescription->status;
                                                        $is_empty = $exam->prescription->items->count() === 0;
                                                    @endphp
                                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase ring-1 ring-inset {{ $status == 'paid' ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-blue-50 text-blue-700 ring-blue-600/20' }}">
                                                        {{ $status }}
                                                    </span>
                                                    @if($status == 'pending')
                                                        <a href="{{ route('doctor.prescriptions.edit', $exam->prescription->id) }}" 
                                                            class="inline-flex items-center justify-center px-3 py-1.5 {{ $is_empty ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-800 hover:bg-blue-900' }} rounded text-[10px] font-bold text-white uppercase transition shadow-md w-full max-w-[120px]">
                                                            {{ $is_empty ? '● Add Meds' : 'Edit (' . $exam->prescription->items->count() . ')' }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <form action="{{ route('doctor.prescriptions.store') }}" method="POST" class="w-full max-w-[120px]">
                                                        @csrf
                                                        <input type="hidden" name="examination_id" value="{{ $exam->id }}">
                                                        <button type="submit" class="w-full px-3 py-1.5 bg-red-600 text-white rounded text-[10px] font-bold uppercase hover:bg-red-700 transition shadow-sm border-b-2 border-red-900">
                                                            + Prescription
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">No history available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>