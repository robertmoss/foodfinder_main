USE food;
DROP PROCEDURE IF EXISTS `getCategories`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCategories`(tenantID int)
BEGIN
	select id,name from category C where C.tenantID=tenantID order by seq;
END$$
DELIMITER ;

USE food;
DROP FUNCTION IF EXISTS `getFeatureViews`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `getFeatureViews`(featureid int) RETURNS int(11)
BEGIN

	select count(*) into @return from pageView where pageType="feature" and pageid=featureid;

	RETURN @return;

END$$
DELIMITER ;

USE food;
DROP PROCEDURE IF EXISTS `addTenant`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addTenant`(_name varchar(100), _title varchar(100), _welcome text,  _css varchar(1000), _allowAnonAccess bit, tenantid int)
BEGIN

	insert into tenant(name,title,welcome,css,allowAnonAccess)
	values (_name,_title,_welcome,_css, _allowAnonAccess);

	SELECT Last_Insert_ID() as newID; 

END$$
DELIMITER ;

USE `food`;
CREATE TABLE IF NOT EXISTS `assignment`(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `name` varchar(100) NOT NULL,
     `description` text,
     `type` varchar(100) NOT NULL,
     `assignedTo` int,
     `targetDate` datetime,
     `status` varchar(100),
     PRIMARY KEY (`id`),
     KEY `fk_assignment_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_assignment_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

USE `food`;
DROP procedure IF EXISTS `getAssignmentById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getAssignmentById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT id,
          name,
          description,
          type,
          assignedTo,
          targetDate,
          status
     FROM
          assignment
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getAssignments`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getAssignments`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT A.id,
          A.name,
          A.description,
          A.type,
          A.assignedTo,
          U.name as assignedToName,
          A.targetDate,
          A.status
     FROM
          assignment A
          left join user U on U.id=A.assignedTo
      WHERE
           A.tenantid=?
	  ORDER BY
		    A.targetDate desc
      LIMIT ?,?";

set @tenantid=tenantid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @start,@num;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `addAssignment`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addAssignment(name varchar(100), description text, type varchar(100), assignedTo int, targetDate datetime, status varchar(100), tenantid int)
BEGIN

     INSERT INTO assignment(
          name,
          description,
          type,
          assignedTo,
          targetDate,
          status,
          tenantid)
     VALUES (name,
          description,
          type,
          assignedTo,
          targetDate,
          status,
          tenantid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateAssignment`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updateAssignment(_id int, name varchar(100), description text, type varchar(100), assignedTo int, targetDate datetime, status varchar(100), _tenantid int)
BEGIN

     UPDATE assignment SET
          name = name,
          description = description,
          type = type,
          assignedTo = assignedTo,
          targetDate = targetDate,
          status = status
     WHERE
          id=_id
          AND tenantid=_tenantid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deleteAssignment`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE deleteAssignment(_id int, _tenantid int, _userid int)
BEGIN

     DELETE FROM assignment WHERE id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getLocationsByEntityListIdEx`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLocationsByEntityListIdEx`(_id int, _tenantid int,_start int, _return int, _userid int)
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
          L.status,
          (select count(*) from visit where userid=? and locationid=L.id) as uservisits,
          C.name as top_category,
          C.icon as icon
		FROM
          entityListItem T1
          INNER JOIN entityList T2 ON T2.id=T1.entityListId
          LEFT JOIN location L on L.ID=T1.entityId
          LEFT JOIN category C on C.id = 
					(select LC.categoryid from locationCategory LC inner join category C 
						on C.id=LC.categoryid where locationid=L.id and C.tenantid=? 
			            order by C.seq
						limit 1)
		WHERE
          T1.entityListId=?
          and T2.tenantid=?
		LIMIT ?,?";

	set @listId=_id;
    set @tenantid = _tenantid;
	set @start=_start;
	set @num=_return;
    set @userid = _userid;

execute stmt using @userid,@tenantid,@listId,@tenantid,@start,@num;
          
          
END$$
DELIMITER ;


USE `food`;
DROP procedure IF EXISTS `getMediaItems`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMediaItems`(_tenantid int, _userid int, _return int, _offset int)
BEGIN

	prepare stmt from "select M.id,M.url,M.name,M.description,
		M.metadata,M.public,M.thumbnailurl,M.height,M.width 
		from media M
	where
		M.tenantid=?
		and (M.public=1 or M.ownerid=?)
	LIMIT ?,?;";
	
    set @tenantid=_tenantid;
    set @userid = _userid;
    set @return = coalesce(_return,10);
    set @offset = coalesce(_offset,0);
    
    execute stmt using @tenantid,@userid,@offset,@return;
            

END$$
DELIMITER ;

ALTER TABLE `food`.`media` 
ADD COLUMN `thumbnailurl` VARCHAR(500) NULL AFTER `public`,
ADD COLUMN `height` INT NULL AFTER `thumbnailurl`,
ADD COLUMN `width` INT NULL AFTER `height`;


USE `food`;
DROP procedure IF EXISTS `updateMedia`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateMedia`(mediaid int, url text, _name varchar(200), description text, public bit, thumbnailurl varchar(500), width int, height int, metadata text, _tenantid int, _ownerid int)
BEGIN

	update media 
    set 
		name=_name,
        description=description,
        metadata=metadata,
        public=public,
        thumbnailurl = thumbnailurl,
        width = width,
        height = height
    where 
		id=mediaid
        and tenantid=_tenantid
		and ownerid=_ownerid;

END$$
DELIMITER ;

ALTER TABLE `food`.`media` 
CHANGE COLUMN `ID` `id` INT(11) NOT NULL AUTO_INCREMENT ;

ALTER TABLE `food`.`media` 
CHANGE COLUMN `metadata` `metadata` text NULL DEFAULT NULL ;

USE `food`;
DROP procedure IF EXISTS `addMedia`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addMedia`(url text,_name varchar(200), description text, public bit, thumbnailurl varchar(500), width int, height int, metadata text, tenantid int, userid int)
BEGIN

	insert into media(url, name,description, public, thumbnailurl, width, height, metadata,tenantid,ownerid)
    values(url, _name,description,public, thumbnailurl, width, height, metadata, tenantid,userid);
    
    SELECT Last_Insert_ID() as newID; 

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getMediaItemsEx`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMediaItemsEx`(_name varchar(200), _description text, _tenantid int, _userid int, _return int, _offset int)
BEGIN

	set @where = "";
    IF _name IS NOT NULL THEN
		set @where = CONCAT(" and M.name like '",_name,"%'");
    END IF;
    
    IF _description IS NOT NULL THEN
		set @where = CONCAT(@where," and M.description like '",_description,"%'");
    END IF;

	set @sql = "select M.id,M.url,M.name,M.description,
		M.metadata,M.public,M.thumbnailurl,M.height,M.width 
		from media M
	where
		M.tenantid=?";
	
    set @sql = CONCAT(@sql,@where,
		" and (M.public=1 or M.ownerid=?) LIMIT ?,?;");
	
    prepare stmt from @sql;
    
    #select @sql;
    
    set @tenantid=_tenantid;
    set @userid = _userid;
    set @return = coalesce(_return,10);
    set @offset = coalesce(_offset,0);
    
    execute stmt using @tenantid,@userid,@offset,@return;
            

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getMediaItemsCountEx`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMediaItemsCountEx`(_name varchar(200), _description text, _tenantid int, _userid int)
BEGIN

	set @where = "";
    IF _name IS NOT NULL THEN
		set @where = CONCAT(" and M.name like '",_name,"%'");
    END IF;
    
    IF _description IS NOT NULL THEN
		set @where = CONCAT(@where," and M.description like '",_description,"%'");
    END IF;

	set @sql = "select count(*)
		from media M
	where
		M.tenantid=?";
	
    set @sql = CONCAT(@sql,@where,
		" and (M.public=1 or M.ownerid=?);");
	
    prepare stmt from @sql;
    
    #select @sql;
    
    set @tenantid=_tenantid;
    set @userid = _userid;
    
    execute stmt using @tenantid,@userid;
            

END$$
DELIMITER ;

USE `food`;
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
          coverImage,
          M.name as coverImageName,
          M.url as coverImageUrl,
          IF(F.status="Published",IF(F.datePosted>NOW(),"Scheduled","Published"),F.status) as status
     FROM	
          feature F
          left join user U on U.id=F.author
          left join media M on F.coverImage = M.id
      WHERE
		F.id=_id AND F.tenantid=_tenantid;

END$$
DELIMITER ;




