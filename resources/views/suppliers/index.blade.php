<x-layouts.app :title="__('Overzicht Leveranciers')">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white dark:bg-zinc-900 shadow ring-1 ring-black/5 dark:ring-white/5 rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $title ?? 'Overzicht Leveranciers' }}</h1>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900 sticky-header">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Naam</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Contactpersoon</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Leveranciernummer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Mobiel</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aantal verschillende producten</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Toon producten</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900">
                        @forelse($suppliers as $s)
                            <tr class="odd:bg-white even:bg-gray-50 odd:dark:bg-zinc-900 even:dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $s->Naam }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $s->ContactPersoon }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $s->Leveranciernummer }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $s->Mobiel }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">{{ $s->AantalVerschillendeProducten }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <a href="{{ route('suppliers.products', ['id' => $s->Id]) }}" class="inline-flex items-center px-3 py-1.5 bg-sky-600 dark:bg-sky-500 text-white text-sm rounded-md hover:bg-sky-700 dark:hover:bg-sky-600">Toon</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">Geen leveranciers gevonden</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</x-layouts.app>
