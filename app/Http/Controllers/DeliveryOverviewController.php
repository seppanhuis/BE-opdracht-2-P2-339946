<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryOverviewController extends Controller
{
    /**
     * User Story 1 - Scenario 01 & 03
     * Show overview of delivered products within a date range
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Default: show all deliveries ever made if no date filter
        $products = [];
        $hasFilter = false;

        if ($startDate && $endDate) {
            $hasFilter = true;

            try {
                // Call stored procedure with date range
                $results = DB::select('CALL sp_GetDeliveredProductsByDateRange(?, ?)', [$startDate, $endDate]);

                // Paginate manually - group by product for pagination (4 records per page)
                $page = $request->input('page', 1);
                $perPage = 4;

                // Group results by ProductId to ensure unique products
                $groupedProducts = [];
                foreach ($results as $row) {
                    $key = $row->ProductId;
                    if (!isset($groupedProducts[$key])) {
                        $groupedProducts[$key] = $row;
                    }
                }

                $products = array_values($groupedProducts);
                $total = count($products);
                $lastPage = ceil($total / $perPage);
                $offset = ($page - 1) * $perPage;

                $products = array_slice($products, $offset, $perPage);

                // Create pagination data
                $pagination = (object) [
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total),
                ];
            } catch (\Exception $e) {
                // Log error
                \Log::error('Error fetching delivered products: ' . $e->getMessage());
                $products = [];
                $pagination = null;
            }
        } else {
            // No filter: show all products ever delivered (for initial load)
            try {
                // Get all deliveries without date filter
                $results = DB::select('
                    SELECT
                        lev.Id AS LeverancierId,
                        lev.Naam AS LeverancierNaam,
                        lev.Contactpersoon,
                        lev.Leveranciernummer,
                        lev.Mobiel,
                        prod.Id AS ProductId,
                        prod.Naam AS ProductNaam,
                        SUM(ppl.Aantal) AS TotaalGeleverd,
                        MIN(ppl.DatumLevering) AS EersteLevering,
                        MAX(ppl.DatumLevering) AS LaatsteLevering
                    FROM
                        ProductPerLeverancier ppl
                    INNER JOIN
                        Leverancier lev ON ppl.LeverancierId = lev.Id
                    INNER JOIN
                        Product prod ON ppl.ProductId = prod.Id
                    WHERE
                        ppl.IsActief = 1
                        AND lev.IsActief = 1
                        AND prod.IsActief = 1
                    GROUP BY
                        lev.Id, lev.Naam, lev.Contactpersoon, lev.Leveranciernummer, lev.Mobiel,
                        prod.Id, prod.Naam
                    ORDER BY
                        lev.Naam ASC, prod.Naam ASC
                ');

                // Paginate
                $page = $request->input('page', 1);
                $perPage = 4;

                // Group by product
                $groupedProducts = [];
                foreach ($results as $row) {
                    $key = $row->ProductId;
                    if (!isset($groupedProducts[$key])) {
                        $groupedProducts[$key] = $row;
                    }
                }

                $products = array_values($groupedProducts);
                $total = count($products);
                $lastPage = ceil($total / $perPage);
                $offset = ($page - 1) * $perPage;

                $products = array_slice($products, $offset, $perPage);

                $pagination = (object) [
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total),
                ];
            } catch (\Exception $e) {
                \Log::error('Error fetching all delivered products: ' . $e->getMessage());
                $products = [];
                $pagination = null;
            }
        }

        return view('delivery.overview', [
            'title' => 'Overzicht geleverde producten',
            'products' => $products,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'hasFilter' => $hasFilter,
            'pagination' => $pagination ?? null,
        ]);
    }

    /**
     * User Story 1 - Scenario 02
     * Show delivery details for a specific product within date range
     */
    public function productDetails(Request $request, $productId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            // Fallback for clicks from overview without active date filter.
            $dateRange = DB::selectOne('
                SELECT
                    MIN(DatumLevering) AS StartDate,
                    MAX(DatumLevering) AS EndDate
                FROM ProductPerLeverancier
                WHERE ProductId = ?
                    AND IsActief = 1
            ', [$productId]);

            if (!$dateRange || !$dateRange->StartDate || !$dateRange->EndDate) {
                return redirect()->route('delivery.overview')
                    ->with('error', 'Geen leveringsdatums gevonden voor dit product');
            }

            $startDate = date('Y-m-d', strtotime($dateRange->StartDate));
            $endDate = date('Y-m-d', strtotime($dateRange->EndDate));
        }

        try {
            // Call stored procedure
            $results = DB::select('CALL sp_GetProductDeliveryDetailsByDateRange(?, ?, ?)',
                [$productId, $startDate, $endDate]);

            // The SP returns multiple result sets, but Laravel only gets the first one
            // So we need to call it differently or restructure

            // Get product info
            $product = DB::selectOne('
                SELECT Id, Naam, Barcode
                FROM Product
                WHERE Id = ? AND IsActief = 1
            ', [$productId]);

            if (!$product) {
                return redirect()->route('delivery.overview')
                    ->with('error', 'Product niet gevonden');
            }

            // Get deliveries for this product in date range
            $deliveries = DB::select('
                SELECT
                    ppl.DatumLevering,
                    ppl.Aantal,
                    ppl.DatumEerstVolgendeLevering,
                    lev.Naam AS LeverancierNaam,
                    lev.Contactpersoon
                FROM
                    ProductPerLeverancier ppl
                INNER JOIN
                    Leverancier lev ON ppl.LeverancierId = lev.Id
                WHERE
                    ppl.ProductId = ?
                    AND ppl.DatumLevering >= ?
                    AND ppl.DatumLevering <= ?
                    AND ppl.IsActief = 1
                    AND lev.IsActief = 1
                ORDER BY
                    ppl.DatumLevering DESC
            ', [$productId, $startDate, $endDate]);

            // Get allergenen
            $allergenen = DB::select('
                SELECT
                    a.Id AS AllergeenId,
                    a.Naam AS AllergeenNaam,
                    a.Omschrijving
                FROM
                    ProductPerAllergeen ppa
                INNER JOIN
                    Allergeen a ON ppa.AllergeenId = a.Id
                WHERE
                    ppa.ProductId = ?
                    AND ppa.IsActief = 1
                    AND a.IsActief = 1
                ORDER BY
                    a.Naam ASC
            ', [$productId]);

            return view('delivery.product-details', [
                'title' => 'Specificatie geleverde producten',
                'product' => $product,
                'deliveries' => $deliveries,
                'allergenen' => $allergenen,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching product delivery details: ' . $e->getMessage());
            return redirect()->route('delivery.overview')
                ->with('error', 'Er is een fout opgetreden bij het ophalen van de gegevens');
        }
    }
}
