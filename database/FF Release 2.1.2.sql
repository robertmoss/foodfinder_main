DELIMITER ;
USE `food`;

ALTER TABLE `food`.`feature` 
ADD COLUMN `coverImage` VARCHAR(500) NULL AFTER `reverseOrder`;

USE `food`;
DROP procedure IF EXISTS `getFeatureById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getFeatureById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT id,
          name,
          headline,
          subhead,
          author,
          datePosted,
          introContent,
          closingContent,
          locationCriteria,
          locationTemplate,
          coalesce(useLocationDesc,0) as useLocationDesc,
          coalesce(numberEntries,0) as numberEntries,
          coalesce(reverseOrder,0) as reverseOrder,
          coverImage
     FROM
          feature
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getFeatures`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getFeatures`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT id,
          name,
          headline,
          subhead,
          author,
          datePosted,
          introContent,
          closingContent,
          locationCriteria,
          locationTemplate,
          useLocationDesc,
          numberEntries,
          reverseOrder,
          coverImage
     FROM
          feature
      WHERE
           tenantid=?
      AND userid=?
      LIMIT ?,?";

set @tenantid=tenantid;
set @userid=userid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @userid,@start,@num;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addFeature`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addFeature(name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, numberEntries bit, reverseOrder bit, coverImage varchar(500), tenantid int, userid int)
BEGIN

     INSERT INTO feature(
          name,
          headline,
          subhead,
          author,
          datePosted,
          introContent,
          closingContent,
          locationCriteria,
          locationTemplate,
          useLocationDesc,
          numberEntries,
          reverseOrder,
          coverImage,
          tenantid,
          userid)
     VALUES (name,
          headline,
          subhead,
          author,
          datePosted,
          introContent,
          closingContent,
          locationCriteria,
          locationTemplate,
          useLocationDesc,
          numberEntries,
          reverseOrder,
          coverImage,
          tenantid,
          userid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateFeature`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updateFeature(_id int, name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, numberEntries bit, reverseOrder bit, coverImage varchar(500), _tenantid int, _userid int)
BEGIN

     UPDATE feature SET
          name = name,
          headline = headline,
          subhead = subhead,
          author = author,
          datePosted = datePosted,
          introContent = introContent,
          closingContent = closingContent,
          locationCriteria = locationCriteria,
          locationTemplate = locationTemplate,
          useLocationDesc = useLocationDesc,
          numberEntries = numberEntries,
          reverseOrder = reverseOrder,
          coverImage = coverImage
     WHERE
          id=_id
          AND tenantid=_tenantid
          AND userid=_userid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deleteFeature`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE deleteFeature(id int, tenant int, userid int)
BEGIN

     DELETE FROM feature WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;



