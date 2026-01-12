<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 border-l-4 border-indigo-500 pl-3">
                        Histori Perubahan Data
                    </h3>
                    <span class="px-3 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded-full">
                        {{ $logs->total() }} Records Terdeteksi
                    </span>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu & User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Modul</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Detail Perubahan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $log->user->name ?? 'System' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $log->created_at->format('d M Y, H:i:s') }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'CREATED' => 'bg-green-100 text-green-700 border-green-200',
                                            'UPDATED' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'DELETED' => 'bg-rose-100 text-rose-700 border-rose-200'
                                        ];
                                        $class = $statusClasses[$log->action] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    @endphp
                                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border {{ $class }}">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-indigo-600">
                                        {{ class_basename($log->model_type) }} 
                                        <span class="text-gray-400 font-sans">#{{ $log->model_id }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <details class="group">
                                        <summary class="flex items-center text-indigo-600 hover:text-indigo-800 cursor-pointer font-medium list-none outline-none">
                                            <svg class="w-4 h-4 mr-1 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            <span class="group-open:hidden">Klik untuk detail</span>
                                            <span class="hidden group-open:inline">Tutup detail</span>
                                        </summary>

                                        <div class="mt-3 overflow-hidden rounded-lg border border-gray-100 shadow-inner">
                                            @if($log->action == 'UPDATED')
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-gray-100">
                                                    <div class="bg-white p-3">
                                                        <span class="text-[10px] font-bold text-rose-500 uppercase tracking-widest">Sebelum</span>
                                                        <pre class="mt-2 text-[11px] leading-relaxed text-gray-600 overflow-x-auto">@json($log->before, JSON_PRETTY_PRINT)</pre>
                                                    </div>
                                                    <div class="bg-white p-3 border-l border-gray-100">
                                                        <span class="text-[10px] font-bold text-green-500 uppercase tracking-widest">Sesudah</span>
                                                        <pre class="mt-2 text-[11px] leading-relaxed text-gray-600 overflow-x-auto">@json($log->after, JSON_PRETTY_PRINT)</pre>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-gray-50 p-4">
                                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Data Objek</span>
                                                    <pre class="mt-2 text-[11px] leading-relaxed text-gray-700 overflow-x-auto">@json($log->after ?? $log->payload, JSON_PRETTY_PRINT)</pre>
                                                </div>
                                            @endif
                                            
                                            <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-400 font-mono">
                                                <span>IP: {{ $log->ip_address }}</span>
                                                <span class="truncate ml-4">UA: {{ $log->user_agent }}</span>
                                            </div>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 italic">Belum ada riwayat aktivitas yang tercatat.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Menghilangkan panah bawaan browser di Chrome/Safari */
        summary::-webkit-details-marker {
            display: none;
        }
        /* Menyesuaikan tampilan scrollbar di area JSON agar tidak memakan tempat */
        pre::-webkit-scrollbar {
            height: 4px;
        }
        pre::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
    </style>
</x-app-layout>