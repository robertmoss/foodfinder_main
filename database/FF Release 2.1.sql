USE `food`;
CREATE TABLE IF NOT EXISTS content(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(100) NOT NULL,
     `defaultText` text NOT NULL,
     `language` varchar(10),
     PRIMARY KEY (`id`)
);

/* Stored Procedures for Content*/

USE `food`;
DROP procedure IF EXISTS `getContentById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE getContentById(id int, tenant int, userid int)
BEGIN

     SELECT id,
          name,
          defaultText,
          language
     FROM
          content
      WHERE
          id=id;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addContent(name varchar(100), defaultText text, language varchar(10), tenantid int)
BEGIN

     INSERT INTO content(
          name,
          defaultText,
          language)
     VALUES (name,
          defaultText,
          language);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updateContent(id int, name varchar(100), defaultText text, language varchar(10), tenantid int)
BEGIN

     UPDATE content SET
          name = name,
          defaultText = defaultText,
          language = language
     WHERE
          id=id
;
END$$
DELIMITER ;USE `food`;
DROP procedure IF EXISTS `deleteContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE deleteContent(id int, tenant int, userid int)
BEGIN

     DELETE FROM content WHERE id=id;

END$$
DELIMITER ;



/* End Content stored procs */

USE `food`;
CREATE TABLE IF NOT EXISTS tenantContent(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `name` varchar(100) NOT NULL,
     `contentText` text,
     `language` varchar(10),
     PRIMARY KEY (`id`),
     KEY `fk_tenantContent_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_tenantContent_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

/* Stored Procedures for TenantContent*/

USE `food`;
DROP procedure IF EXISTS `getTenantContentById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE getTenantContentById(id int, tenant int, userid int)
BEGIN

     SELECT id,
          name,
          contentText,
          language
     FROM
          tenantContent
      WHERE
          id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addTenantContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addTenantContent(_name varchar(100), contentText text, language varchar(10), tenantid int)
BEGIN

	-- check to see if there is base content for this key; if not, create it
    SET @baseCount = (select count(*) from content where name=_name);
    IF @baseCount=0 THEN
		INSERT INTO content(name,defaultText,language)
			VALUES (_name,contentText,language);
    END IF;

     INSERT INTO tenantContent(
          name,
          contentText,
          language,
          tenantid)
     VALUES (_name,
          contentText,
          language,
          tenantid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateTenantContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updateTenantContent(id int, name varchar(100), contentText text, language varchar(10), tenantid int)
BEGIN

     UPDATE tenantContent SET
          name = name,
          contentText = contentText,
          language = language
     WHERE
          id=id
          AND tenantid=tenantid;
END$$
DELIMITER ;USE `food`;
DROP procedure IF EXISTS `deleteTenantContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE deleteTenantContent(id int, tenant int, userid int)
BEGIN

     DELETE FROM tenantContent WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getTenantContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE getTenantContent(_name varchar(100), _tenantid int, _language varchar(10))
BEGIN

	select
		coalesce(TC.id,0) as id,
        C.name as name,
		coalesce(contentText,defaultText) as contentText,
        _language as language
	from content C 
		left join tenantContent TC
		on C.name = TC.name and TC.tenantid=_tenantid
	where 
		C.name=_name 
		and C.language = coalesce(_language,'en_US');


END$$
DELIMITER ;


/* End TenantContent stored procs */

USE `food`;
CREATE TABLE IF NOT EXISTS feature(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `userid` int(11) NOT NULL,
     `name` varchar(100) NOT NULL,
     `headline` varchar(300) NOT NULL,
     `subhead` varchar(300),
     `author` varchar(200),
     `datePosted` datetime,
     `introContent` text,
     `closingContent` text,
     `locationCriteria` varchar(500),
     `locationTemplate` text,
     `useLocationDesc` bit,
     PRIMARY KEY (`id`),
     KEY `fk_feature_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_feature_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

/* Stored Procedures for Feature*/

USE `food`;
DROP procedure IF EXISTS `getFeatureById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE getFeatureById(id int, tenant int, userid int)
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
          useLocationDesc
     FROM
          feature
      WHERE
          id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addFeature`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addFeature(name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, tenantid int, userid int)
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
          tenantid,
          userid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateFeature`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updateFeature(id int, name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, tenantid int, userid int)
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
          useLocationDesc = useLocationDesc
     WHERE
          id=id
          AND tenantid=tenantid
          AND userid=userid;
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

USE `food`;
DROP procedure IF EXISTS `getFeatures`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE getFeatures(userid int, numToReturn int,startAt int,tenantid int)
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
          useLocationDesc
     FROM
          feature
      WHERE
           tenantid=tenantid
      LIMIT ?,?";

set @start=startAt;
set @num=numToReturn;

execute stmt using @start,@num;

END$$
DELIMITER ;


/* End Feature stored procs */

