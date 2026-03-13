<x-layouts.app :title="$title">
    @push('head')
        <style>
            /* sticky header background for light mode */
            .sticky-header th {
                position: sticky;
                top: 0;
                background: white;
                z-index: 5;
            }

            /* sticky header for dark mode */
            .dark .sticky-header th {
                background: #0b1220;
                border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            }

            /* subtle row divider in dark mode for better separation */
            .dark tbody tr td {
                border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            }
        </style>
    @endpush

    <main class="flex-1 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="bg-white dark:bg-zinc-900 shadow ring-1 ring-black/5 dark:ring-white/5 rounded-lg p-6">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $title }}
                        </h1>
                        <a
                            href="{{ route('delivery.overview', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-zinc-700 text-gray-700 dark:text-white text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-zinc-600"
                        >
                            ← Terug naar overzicht
                        </a>
                    </div>

                    <!-- Selected Date Range -->
                    <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <strong>Tijdsvak:</strong> {{ date('d-m-Y', strtotime($startDate)) }} t/m {{ date('d-m-Y', strtotime($endDate)) }}
                        </p>
                    </div>
                </div>

                <!-- Product Information -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Productinformatie
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Productnaam:</span>
                            <span class="text-gray-900 dark:text-white ml-2">{{ $product->Naam }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Barcode:</span>
                            <span class="text-gray-900 dark:text-white ml-2">{{ $product->Barcode }}</span>
                        </div>
                    </div>
                </div>

                <!-- Allergenen -->
                @if(count($allergenen) > 0)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-2">
                            Allergenen
                        </h3>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($allergenen as $allergeen)
                                <li class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong>{{ $allergeen->AllergeenNaam }}:</strong> {{ $allergeen->Omschrijving }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Delivery History Table -->
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        Leveringsdata
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="sticky-header bg-gray-50 dark:bg-zinc-900">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Datum Levering
                                </th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Aantal
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Leverancier
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Contactpersoon
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                            @forelse ($deliveries as $delivery)
                                <tr class="odd:bg-white even:bg-gray-50 odd:dark:bg-zinc-900 even:dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ date('d-m-Y', strtotime($delivery->DatumLevering)) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                        {{ $delivery->Aantal }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $delivery->LeverancierNaam }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $delivery->Contactpersoon }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Er zijn geen leveringen gevonden voor dit product in de geselecteerde periode.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</x-layouts.app>
