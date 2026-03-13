-- ************************************************************************************************
-- Doel: Stored procedure to get all delivered products within a date range with total amounts
-- ************************************************************************************************
-- Versie  Datum         Auteur              Beschrijving
-- ******  *********     *****************   ******************************************************
-- 01      10-03-2026    Copilot             Nieuwe stored procedure voor user story 1
-- ************************************************************************************************

DROP PROCEDURE IF EXISTS sp_GetDeliveredProductsByDateRange;

DELIMITER //

CREATE PROCEDURE sp_GetDeliveredProductsByDateRange(
    IN p_StartDatum DATE,
    IN p_EindDatum DATE
)
BEGIN
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
        ppl.DatumLevering >= p_StartDatum
        AND ppl.DatumLevering <= p_EindDatum
        AND ppl.IsActief = 1
        AND lev.IsActief = 1
        AND prod.IsActief = 1
    GROUP BY
        lev.Id, lev.Naam, lev.Contactpersoon, lev.Leveranciernummer, lev.Mobiel,
        prod.Id, prod.Naam
    ORDER BY
        lev.Naam ASC, prod.Naam ASC;
END //

DELIMITER ;
