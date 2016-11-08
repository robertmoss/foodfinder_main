USE food;
DROP PROCEDURE IF EXISTS `getCategories`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCategories`(tenantID int)
BEGIN
	select id,name from category C where C.tenantID=tenantID order by seq;
END$$
DELIMITER ;



