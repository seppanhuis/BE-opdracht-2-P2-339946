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

            /* Smooth transition for table rows */
            tbody tr {
                transition: background-color 0.2s ease-in-out;
            }
        </style>
    @endpush

    <main class="flex-1 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="bg-white dark:bg-zinc-900 shadow ring-1 ring-black/5 dark:ring-white/5 rounded-lg p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
                        {{ $title }}
                    </h1>

                    <!-- Date Filter Form -->
                    <form method="GET" action="{{ route('delivery.overview') }}" class="bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Startdatum
                                </label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ $startDate ?? '' }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-zinc-700 dark:text-white"
                                >
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Einddatum
                                </label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    value="{{ $endDate ?? '' }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-zinc-700 dark:text-white"
                                >
                            </div>
                            <div class="flex gap-2">
                                <button
                                    type="submit"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-red-600 dark:bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    Maak Selectie
                                </button>
                                @if($hasFilter)
                                <a
                                    href="{{ route('delivery.overview') }}"
                                    class="inline-flex justify-center items-center px-4 py-2 bg-gray-300 dark:bg-zinc-600 text-gray-700 dark:text-white text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-zinc-500"
                                >
                                    Reset
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                @if($hasFilter && count($products) === 0)
                    <!-- Scenario 03: No deliveries in this period -->
                    <div class="text-center py-10">
                        <p class="text-gray-500 dark:text-gray-400 text-lg">
                            Er zijn geen leveringen geweest van producten in deze periode
                        </p>
                    </div>
                @else
                    <!-- Table showing delivered products -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead class="sticky-header bg-gray-50 dark:bg-zinc-900">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Naam Leverancier
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Contactpersoon
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Productnaam
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Totaal geleverd
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Specificatie
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                                @forelse ($products as $product)
                                    <tr class="odd:bg-white even:bg-gray-50 odd:dark:bg-zinc-900 even:dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $product->LeverancierNaam }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $product->Contactpersoon }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $product->ProductNaam }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                            {{ $product->TotaalGeleverd }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <a
                                                href="{{ route('delivery.product-details', ['productId' => $product->ProductId, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 dark:bg-blue-500 text-white text-lg font-bold rounded-full hover:bg-blue-700 dark:hover:bg-blue-600"
                                                title="Bekijk specificatie"
                                            >
                                                ?
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Selecteer een datumbereik om geleverde producten te bekijken.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($pagination) && $pagination->last_page > 1)
                        <div class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-zinc-700 pt-4">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Toont {{ $pagination->from }} tot {{ $pagination->to }} van {{ $pagination->total }} resultaten
                            </div>
                            <div class="flex gap-2">
                                @if($pagination->current_page > 1)
                                    <a
                                        href="{{ route('delivery.overview', ['start_date' => $startDate, 'end_date' => $endDate, 'page' => $pagination->current_page - 1]) }}"
                                        class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600"
                                    >
                                        Vorige
                                    </a>
                                @endif

                                @for($i = 1; $i <= $pagination->last_page; $i++)
                                    <a
                                        href="{{ route('delivery.overview', ['start_date' => $startDate, 'end_date' => $endDate, 'page' => $i]) }}"
                                        class="px-4 py-2 {{ $i === $pagination->current_page ? 'bg-red-600 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300' }} rounded-md hover:bg-red-700 dark:hover:bg-red-600"
                                    >
                                        {{ $i }}
                                    </a>
                                @endfor

                                @if($pagination->current_page < $pagination->last_page)
                                    <a
                                        href="{{ route('delivery.overview', ['start_date' => $startDate, 'end_date' => $endDate, 'page' => $pagination->current_page + 1]) }}"
                                        class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600"
                                    >
                                        Volgende
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </main>
</x-layouts.app>
