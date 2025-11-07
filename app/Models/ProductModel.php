<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductModel extends Model
{
    public function sp_GetAllProducts()
    {
        return DB::select('CALL sp_GetAllProducts()');
    }
    /**
     * Call stored procedure sp_GetLeverantieInfo which returns supplier basic info
     * (Naam, Contactpersoon, Leveranciernummer, Mobiel) for a product.
     */
    public function sp_GetLeverantieInfo(int $productId)
    {
        return DB::select('CALL sp_GetLeverantieInfo(?)', [$productId]);
    }

    /**
     * Call stored procedure sp_GetLeverancierInfo which returns delivery history
     * (DatumLevering, Aantal, DatumEerstVolgendeLevering, AantalAanwezig) for a product.
     */
    public function sp_GetLeverancierInfo(int $productId)
    {
        return DB::select('CALL sp_GetLeverancierInfo(?)', [$productId]);
    }

    /**
     * Stored procedure: get supplier overview with count of distinct products
     */
    public function sp_GetSupplierOverview()
    {
        return DB::select('CALL sp_GetSupplierOverview()');
    }

    /**
     * Stored procedure: get products delivered by a supplier with stock and last delivery
     */
    public function sp_GetProductsBySupplier(int $leverancierId)
    {
        return DB::select('CALL sp_GetProductsBySupplier(?)', [$leverancierId]);
    }

    /**
     * Stored procedure: add a delivery record and update magazijn
     * returns the updated magazijn row or throws SQL error when SP signals
     */
    public function sp_AddDelivery(int $leverancierId, int $productId, int $aantal, string $datumLevering, ?string $datumEerstVolgende)
    {
        // Use CALL which may return a resultset
        $params = [$leverancierId, $productId, $aantal, $datumLevering, $datumEerstVolgende];
        return DB::select('CALL sp_AddDelivery(?,?,?,?,?)', $params);
    }

    /**
     * Get allergenen for a given product (joins ProductPerAllergeen -> Allergeen)
     */
    public function getProductAllergenen(int $productId)
    {
        return DB::select('CALL sp_GetProductAllergenen(?)', [$productId]);
    }

    /**
     * Get product basic info (Naam, Barcode) by id
     */
    public function getProductById(int $productId)
    {
        $rows = DB::select('CALL sp_GetProductById(?)', [$productId]);
        return count($rows) ? $rows[0] : null;
    }

    /**
     * Get supplier basic info by id
     */
    public function getSupplierById(int $supplierId)
    {
        // Use stored procedure to keep SQL centralized and ensure aliasing consistency
        $rows = DB::select('CALL sp_GetSupplierById(?)', [$supplierId]);
        return count($rows) ? $rows[0] : null;
    }
}
