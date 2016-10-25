

USE `food`;
DROP procedure IF EXISTS `getLocationsBySearchCriteria`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLocationsBySearchCriteria`(tenantid int, _name varchar(100), state varchar(10), numToReturn integer, startAt integer)
BEGIN

set @searchCriteria = '';

IF LENGTH(_name)>0 THEN
		set @searchCriteria = concat("and L.name like ","'",replace(_name,"'","''"),"%'");
END IF;

IF LENGTH(state)>0 THEN
		set @searchCriteria = concat(@searchCriteria, " and L.state='",state,"'");
END IF;

set @sql = concat(" select 
						L.id, L.name, L.address, L.city, L.state, L.phone, L.url, L.imageurl, L.latitude, L.longitude,
                        L.shortdesc as shortdescription, L.status,
						L.googleReference as googleReference, replace(L.name,'''',?) as linkname,
                        C.name as top_category, C.icon as icon
 					from 
 						location L
                        left join shareLocation SL on SL.locationid = L.id
                        left join category C on C.id = 
							(select LC.categoryid from locationCategory LC inner join category C 
								on C.id=LC.categoryid where locationid=L.id and C.tenantid=? 
							 union
                             select C2.id from category C2 
								where C2.id=SL.forceCategoryId
						     order by seq
							 limit 1)
 					where 
						(L.tenantid=? or SL.sharedTenantId=?)",
                        @searchCriteria);

set @sql = concat(@sql,						
					" order by
						name
					limit ?,?");

                    
prepare stmt from @sql;

set @tenantid = tenantid;
set @start = startAt;
set @num = numToReturn;
set @rep = '\\\'';

execute stmt using @rep, @tenantid, @tenantid, @tenantid, @start, @num;


END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getAuthorListByTenant`;

DELIMITER $$
USE `food`$$
CREATE PROCEDURE `getAuthorListByTenant` (_tenantid int)
BEGIN

	select distinct U.id, U.name,U.email, R.name as role from user U
	inner join tenantUser TR on U.id=TR.userid
    inner join tenantUserRole TUR on TUR.tenantuserid=TR.id
    inner join role R on TUR.roleid=R.id
    where 
		TR.tenantid=_tenantid
        and (R.name="contributor" or R.name="admin");


END$$

DELIMITER ;

use food;

ALTER TABLE feature DROP COLUMN author;

ALTER TABLE feature ADD COLUMN author int;

USE `food`;
DROP procedure IF EXISTS `getFeatureById`;

DELIMITER $$
USE `food`$$
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
          coverImage
     FROM
          feature F
          left join user U on U.id=F.author
      WHERE
		F.id=_id AND F.tenantid=_tenantid;

END$$

DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getFeatures`;

DELIMITER $$
USE `food`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFeatures`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT F.id,
          F.name,
          F.headline,
          F.subhead,
          U.name as author,
          F. datePosted,
          F. coverImage
     FROM
          feature F
          left join user U on U.id=F.author
      WHERE
           tenantid=?
      LIMIT ?,?";

set @tenantid=tenantid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @start,@num;

END$$

USE `food`;
DROP procedure IF EXISTS `getFeaturesByAuthor`;

DELIMITER $$
USE `food`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFeaturesByAuthor`(authorid int, userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT F.id,
          F.name,
          F.headline,
          F.subhead,
          U.name as author,
          F. datePosted,
          F. coverImage
     FROM
          feature F
          left join user U on U.id=F.author
      WHERE
           tenantid=?
		   AND author=?
      
      LIMIT ?,?";

set @tenantid=tenantid;
set @authorid=authorid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @authorid,@start,@num;

END$$



