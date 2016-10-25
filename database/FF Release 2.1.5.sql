use food;
ALTER TABLE `food`.`feature` 
ADD COLUMN `isNewsItem` VARCHAR(45) NULL AFTER `reverseOrder`;


use food;
DROP procedure IF EXISTS `addFeature`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addFeature`(name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, numberEntries bit, reverseOrder bit, isNewsItem bit, coverImage varchar(500), tenantid int, userid int)
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
          isNewsItem,
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
          isNewsItem,
          coverImage,
          tenantid,
          userid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

DROP procedure IF EXISTS `updateFeature`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateFeature`(_id int, name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, numberEntries bit, reverseOrder bit, isNewsItem bit, coverImage varchar(500), _tenantid int, _userid int)
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
          isNewsItem = isNewsItem,
          coverImage = coverImage
     WHERE
          id=_id
          AND tenantid=_tenantid
          AND userid=_userid;
END$$
DELIMITER ;

DROP procedure IF EXISTS `getFeatureById`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFeatureById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT F.id,
          F.name,
          headline,
          subhead,
          U.id as author,
          U.name as authorName,
          datePosted,
          introContent,
          closingContent,
          locationCriteria,
          locationTemplate,
          coalesce(useLocationDesc,0) as useLocationDesc,
          coalesce(numberEntries,0) as numberEntries,
          coalesce(reverseOrder,0) as reverseOrder,
		  coalesce(isNewsItem,0) as isNewsItem,
          coverImage
     FROM
          feature F
          left join user U on U.id=F.author
      WHERE
		F.id=_id AND F.tenantid=_tenantid;

END$$
DELIMITER ;


DROP procedure IF EXISTS `getFeaturesNewsItems`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFeaturesNewsItems`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT F.id,
          F.name,
          F.headline,
          F.subhead,
          U.name as author,
          U.id as authorid,
          F. datePosted,
          F. coverImage
     FROM
          feature F
          left join user U on U.id=F.author
      WHERE
           tenantid=?
		   AND isNewsItem=1
      ORDER BY
		   F.datePosted DESC
      LIMIT ?,?";

set @tenantid=tenantid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @start,@num;

END$$
DELIMITER ;


