-- ************************************************************************************************
-- Doel: Stored procedure to get delivery details for a specific product within a date range
-- ************************************************************************************************
-- Versie  Datum         Auteur              Beschrijving
-- ******  *********     *****************   ******************************************************
-- 01      10-03-2026    Copilot             Nieuwe stored procedure voor user story 1 scenario 2
-- ************************************************************************************************

DROP PROCEDURE IF EXISTS sp_GetProductDeliveryDetailsByDateRange;

DELIMITER //

CREATE PROCEDURE sp_GetProductDeliveryDetailsByDateRange(
    IN p_ProductId INT,
    IN p_StartDatum DATE,
    IN p_EindDatum DATE
)
BEGIN
    -- Get product basic info first
    SELECT
        prod.Id AS ProductId,
        prod.Naam AS ProductNaam,
        prod.Barcode
    FROM
        Product prod
    WHERE
        prod.Id = p_ProductId
        AND prod.IsActief = 1
    LIMIT 1;

    -- Get delivery details for this product in the date range
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
        ppl.ProductId = p_ProductId
        AND ppl.DatumLevering >= p_StartDatum
        AND ppl.DatumLevering <= p_EindDatum
        AND ppl.IsActief = 1
        AND lev.IsActief = 1
    ORDER BY
        ppl.DatumLevering DESC;

    -- Get allergenen for this product
    SELECT
        a.Id AS AllergeenId,
        a.Naam AS AllergeenNaam,
        a.Omschrijving
    FROM
        ProductPerAllergeen ppa
    INNER JOIN
        Allergeen a ON ppa.AllergeenId = a.Id
    WHERE
        ppa.ProductId = p_ProductId
        AND ppa.IsActief = 1
        AND a.IsActief = 1
    ORDER BY
        a.Naam ASC;
END //

DELIMITER ;
