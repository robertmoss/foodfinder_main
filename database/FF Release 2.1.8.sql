
USE food;
DROP FUNCTION IF EXISTS `getFeatureViews`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `getFeatureViews`(featureid int) RETURNS int
BEGIN

	select count(*) into @return from pageView where pageType="feature" and pageid=featureid;

	RETURN @return;

END$$
DELIMITER ;

USE food;
DROP PROCEDURE IF EXISTS `getFeaturesEx`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFeaturesEx`(authorid int, isNewsItem int, status varchar(100), returnExtendedFields bit, userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     set @select = "SELECT F.id,
          F.name,
          F.headline,
          F.subhead,
          U.name as author,
          F.datePosted,
          F.coverImage,
          IF(F.status=\"Published\",IF(F.datePosted>NOW(),\"Scheduled\",\"Published\"),F.status) as status";
	
     IF (returnExtendedFields=1) THEN
		 set @select = concat(@select, ", U.id as authorid, getFeatureViews(F.id) as views");
     END IF;
     
     set @from = " FROM
          feature F
          left join user U on U.id=F.author
      WHERE
           tenantid=?";
           
	set @where="";
    
    IF (authorid IS NOT NULL) THEN
		set @where = concat(@where," AND author=",authorid);
    END IF;
    IF (isNewsItem IS NOT NULL) THEN
		set @where = concat(@where," AND isNewsItem=",isNewsItem);
    END IF;
    IF (status IS NOT NULL) THEN
		set @where = concat(@where," AND IF(F.status=\"Published\",IF(F.datePosted>NOW(),\"Scheduled\",\"Published\"),F.status)=\"",status,"\"");
    END IF;
    
    set @orderby = " ORDER BY F.datePosted DESC LIMIT ?,?";
    
	set @sql = concat(@select,@from,@where,@orderby);
      
prepare stmt from @sql;

set @tenantid=tenantid;
set @start=startAt;
set @num=numToReturn;

execute stmt using @tenantid, @start,@num;

END$$
DELIMITER ;

USE food;
DROP PROCEDURE IF EXISTS `getFeatureById`;

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
          IF(F.status="Published",IF(F.datePosted>NOW(),"Scheduled","Published"),F.status) as status
     FROM	
          feature F
          left join user U on U.id=F.author
      WHERE
		F.id=_id AND F.tenantid=_tenantid;

END$$
DELIMITER ;


