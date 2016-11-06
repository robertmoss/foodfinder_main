use food;
DROP procedure IF EXISTS `getFeaturesByAuthor`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFeaturesByAuthor`(authorid int, userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT F.id,
          F.name,
          F.headline,
          F.subhead,
          U.name as author,
          F.datePosted,
          F.coverImage
     FROM
          feature F
          left join user U on U.id=F.author
      WHERE
           tenantid=?
		   AND author=?
      ORDER BY
		   F.datePosted DESC
      LIMIT ?,?";

set @tenantid=tenantid;
set @authorid=authorid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @authorid,@start,@num;

END$$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `food`.`pageView` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pageType` VARCHAR(100) NULL,
  `pageId` INT NULL,
  `sessionId` VARCHAR(100) NULL,
  `additionalData` VARCHAR(1000) NULL,
PRIMARY KEY (`id`));

ALTER TABLE `food`.`pageView` 
ADD COLUMN `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `additionalData`;




