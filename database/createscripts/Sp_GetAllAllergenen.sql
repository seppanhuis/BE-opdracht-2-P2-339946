DROP PROCEDURE IF EXISTS Sp_GetAllAllergenen;

DELIMITER $$

CREATE PROCEDURE Sp_GetAllAllergenen()
BEGIN

    SELECT ALGE.Id
          ,ALGE.Naam
          ,ALGE.Omschrijving
    FROM  Allergeen AS ALGE;


END$$

DELIMITER ;