DELIMITER ;

USE `food`;
CREATE TABLE IF NOT EXISTS content(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(100) NOT NULL,
     `defaultText` text NOT NULL,
     `language` varchar(10),
     PRIMARY KEY (`id`)
);


USE `food`;
DROP procedure IF EXISTS `getContentById`;

DELIMITER $$
USE `food`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getContentById`(id int, tenant int, userid int)
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
CREATE PROCEDURE `addContent`(name varchar(100), defaultText text, language varchar(10), tenantid int)
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
CREATE PROCEDURE `updateContent`(id int, name varchar(100), defaultText text, language varchar(10), tenantid int)
BEGIN

     UPDATE content SET
          name = name,
          defaultText = defaultText,
          language = language
     WHERE
          id=id
;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deleteContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `deleteContent`(id int, tenant int, userid int)
BEGIN

     DELETE FROM content WHERE id=id;

END$$
DELIMITER ;


USE `food`;
CREATE TABLE IF NOT EXISTS `tenantContent`(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `name` varchar(100) NOT NULL,
     `contentText` text,
     `language` varchar(10),
     PRIMARY KEY (`id`),
     KEY `fk_tenantContent_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_tenantContent_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);


USE `food`;
DROP procedure IF EXISTS `getTenantContentById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getTenantContentById`(id int, tenant int, userid int)
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

CREATE PROCEDURE `addTenantContent`(_name varchar(100), contentText text, language varchar(10), tenantid int)
BEGIN

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

CREATE PROCEDURE `updateTenantContent`(id int, name varchar(100), contentText text, language varchar(10), tenantid int)
BEGIN

     UPDATE tenantContent SET
          name = name,
          contentText = contentText,
          language = language
     WHERE
          id=id
          AND tenantid=tenantid;
END$$

DELIMITER ;
USE `food`;

DROP procedure IF EXISTS `deleteTenantContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `deleteTenantContent`(id int, tenant int, userid int)
BEGIN

     DELETE FROM tenantContent WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getTenantContent`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getTenantContent`(_name varchar(100), _tenantid int, _language varchar(10))
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


USE `food`;
CREATE TABLE IF NOT EXISTS `feature`(
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
          useLocationDesc
     FROM
          feature
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addFeature`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `addFeature`(name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, tenantid int, userid int)
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

CREATE PROCEDURE `updateFeature`(_id int, name varchar(100), headline varchar(300), subhead varchar(300), author varchar(200), datePosted datetime, introContent text, closingContent text, locationCriteria varchar(500), locationTemplate text, useLocationDesc bit, _tenantid int, _userid int)
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
          id=_id
          AND tenantid=_tenantid
          AND userid=_userid;
END$$
DELIMITER ;


USE `food`;
DROP procedure IF EXISTS `deleteFeature`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `deleteFeature`(id int, tenant int, userid int)
BEGIN

     DELETE FROM feature WHERE id=id AND tenantid=tenantid;

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




USE `food`;
DROP procedure IF EXISTS `getLocations`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getLocations`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT id,
          name,
          address,
          city,
          state,
          phone,
          url,
          imageurl,
          latitude,
          longitude,
          shortdesc,
          googleReference,
          googlePlacesId,
          status
     FROM
          location
      WHERE
           tenantid=?
      LIMIT ?,?";

set @tenantid=tenantid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid,@start,@num;

END$$
DELIMITER ;

USE `food`;
CREATE TABLE IF NOT EXISTS `entityList`(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `userid` int(11) NOT NULL,
     `name` varchar(300) NOT NULL,
     `description` varchar(2000),
     `type` varchar(100),
     `entity` varchar(100),
     PRIMARY KEY (`id`),
     KEY `fk_entityList_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_entityList_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);


USE `food`;
DROP procedure IF EXISTS `getEntityListById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getEntityListById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT id,
          name,
          description,
          type,
          entity
     FROM
          entityList
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getEntityListItemsByEntityListId`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getEntityListItemsByEntityListId`(_id int, _tenantid int, userid int)
BEGIN

     SELECT
          T1.id,
          T1.entityId,
          T1.sequence     
FROM
          entityListItem T1
          INNER JOIN entityList T2 ON T2.id=T1.entityListId
     WHERE
          T1.entityListId=_id
          and T2.tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `removeEntityListEntityListItems`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `removeEntityListEntityListItems`(_entityListid int, _tenantid int)
BEGIN

     SET SQL_SAFE_UPDATES = 0;
     DELETE FROM entityListItem WHERE id in (
          select * from (select distinct T1.id from
          entityListItem T1
          inner join entityList T2 on T2.tenantid=_tenantid and T1.entityListid=T2.id
     WHERE
          T1.entityListid=_entityListid) as list);
     SET SQL_SAFE_UPDATES = 1;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addEntityListEntityListItem`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `addEntityListEntityListItem`(_entityListid int, _entityid int, _tenantid int)
BEGIN

	select coalesce(max(sequence),0)+1 into @sequence from entityListItem where entityListId=_entityListId;
	
	insert into entityListItem(tenantid,entityListId,entityId,sequence)
    values (_tenantid,_entityListId,_entityid, @sequence);
	
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getEntityLists`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getEntityLists`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT id,
          name,
          description,
          type,
          entity
     FROM
          entityList
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
DROP procedure IF EXISTS `addEntityList`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `addEntityList`(name varchar(300), description varchar(2000), type varchar(100), entity varchar(100), tenantid int, userid int)
BEGIN

     INSERT INTO entityList(
          name,
          description,
          type,
          entity,
          tenantid,
          userid)
     VALUES (name,
          description,
          type,
          entity,
          tenantid,
          userid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateEntityList`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `updateEntityList`(_id int, name varchar(300), description varchar(2000), type varchar(100), entity varchar(100), _tenantid int, _userid int)
BEGIN

     UPDATE entityList SET
          name = name,
          description = description,
          type = type,
          entity = entity
     WHERE
          id=_id
          AND tenantid=_tenantid
          AND userid=_userid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deleteEntityList`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `deleteEntityList`(id int, tenant int, userid int)
BEGIN

     DELETE FROM entityList WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;



USE `food`;
CREATE TABLE IF NOT EXISTS `entityListItem`(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `entityListId` int(11) NOT NULL,
     `entityId` int(11),
     `sequence` int(11),
     PRIMARY KEY (`id`),
     KEY `fk_entityListItem_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_entityListItem_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
     KEY `fk_entityListItem_entityList_idx` (`entityListId`),
     CONSTRAINT `fk_entityListItem_entityList` FOREIGN KEY (`entityListId`) REFERENCES `entityList` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);


USE `food`;
DROP procedure IF EXISTS `getEntityListItemById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getEntityListItemById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT id,
          entityListId,
          entityId,
          sequence
     FROM
          entityListItem
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getEntityListItems`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getEntityListItems`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT id,
          entityListId,
          entityId,
          sequence
     FROM
          entityListItem
      WHERE
           tenantid=?
      LIMIT ?,?";

set @tenantid=tenantid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @start,@num;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addEntityListItem`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `addEntityListItem`(entityListId int(11), entityId int(11), sequence int(11), tenantid int)
BEGIN

     INSERT INTO entityListItem(
          entityListId,
          entityId,
          sequence,
          tenantid)
     VALUES (entityListId,
          entityId,
          sequence,
          tenantid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateEntityListItem`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `updateEntityListItem`(_id int, entityListId int(11), entityId int(11), sequence int(11), _tenantid int)
BEGIN

     UPDATE entityListItem SET
          entityListId = entityListId,
          entityId = entityId,
          sequence = sequence
     WHERE
          id=_id
          AND tenantid=_tenantid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deleteEntityListItem`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `deleteEntityListItem`(id int, tenant int, userid int)
BEGIN

     DELETE FROM entityListItem WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getLocationsByEntityListId`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getLocationsByEntityListId`(_id int, _tenantid int)
BEGIN

SELECT
          T1.id,
          T1.entityId,
          L.name,
          L.city,
          L.state,
          T1.sequence     
FROM
          entityListItem T1
          INNER JOIN entityList T2 ON T2.id=T1.entityListId
          LEFT JOIN location L on L.ID=T1.entityId
     WHERE
          T1.entityListId=_id
          and T2.tenantid=_tenantid;
END$$
DELIMITER ;


USE `food`;
DROP procedure IF EXISTS `getLocationsByEntityListIdEx`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getLocationsByEntityListIdEx`(_id int, _tenantid int,_start int, _return int)
BEGIN

	prepare stmt from "SELECT
          L.id,
          L.name,
          L.address,
          L.city,
          L.state,
          L.phone,
          L.url,
          L.imageurl,
          L.latitude,
          L.longitude,
          L.shortdesc,
          L.googleReference,
          L.googlePlacesId,
          L.status
		FROM
          entityListItem T1
          INNER JOIN entityList T2 ON T2.id=T1.entityListId
          LEFT JOIN location L on L.ID=T1.entityId
		WHERE
          T1.entityListId=?
          and T2.tenantid=?
		LIMIT ?,?";

	set @listId=_id;
    set @tenantid = _tenantid;
	set @start=_start;
	set @num=_return;

execute stmt using @listId,@tenantid,@start,@num;
          
          
END$$
DELIMITER ;
