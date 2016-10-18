

USE `food`;
CREATE TABLE IF NOT EXISTS `product`(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `userid` int(11) NOT NULL,
     `name` varchar(100) NOT NULL,
     `url` varchar(500),
     `title` varchar(300),
     `author` varchar(200),
     `description` text,
     `price` numeric(10,2),
     `imageUrl` varchar(500),
     PRIMARY KEY (`id`),
     KEY `fk_product_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_product_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

USE `food`;
DROP procedure IF EXISTS `getProductById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getProductById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT id,
          name,
          url,
          title,
          author,
          description,
          price,
          imageUrl
     FROM
          product
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getProducts`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getProducts`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT id,
          name,
          url,
          title,
          author,
          description,
          price,
          imageUrl
     FROM
          product
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
DROP procedure IF EXISTS `addProduct`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addProduct(name varchar(100), url varchar(500), title varchar(300), author varchar(200), description text, price numeric(10,2), imageUrl varchar(500), tenantid int, userid int)
BEGIN

     INSERT INTO product(
          name,
          url,
          title,
          author,
          description,
          price,
          imageUrl,
          tenantid,
          userid)
     VALUES (name,
          url,
          title,
          author,
          description,
          price,
          imageUrl,
          tenantid,
          userid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateProduct`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updateProduct(_id int, name varchar(100), url varchar(500), title varchar(300), author varchar(200), description text, price numeric(10,2), imageUrl varchar(500), _tenantid int, _userid int)
BEGIN

     UPDATE product SET
          name = name,
          url = url,
          title = title,
          author = author,
          description = description,
          price = price,
          imageUrl = imageUrl
     WHERE
          id=_id
          AND tenantid=_tenantid
          AND userid=_userid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deleteProduct`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE deleteProduct(id int, tenant int, userid int)
BEGIN

     DELETE FROM product WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getEntitiesByEntityListId`;

DELIMITER $$
USE `food`$$
CREATE  PROCEDURE `getEntitiesByEntityListId`(_id int, _tenantid int)
BEGIN

	declare entityName varchar(100);

	select EL.entity into entityName from entityList EL where EL.id=_id;
    
    set @fieldText = ",E.name as displayText";
    
    IF entityName="location" THEN
        set @fieldText = ",concat(E.name,\" (\", E.city, \", \" ,E.state, \")\") as displayText";
        END IF;
        

	set @sql1 = concat("SELECT
			  T1.id,
			  T1.entityId,
			  E.name",@fieldText, ",
			  T1.sequence     
	FROM
			  entityListItem T1
			  INNER JOIN entityList T2 ON T2.id=T1.entityListId");
			  
	set @sql2 = concat(" LEFT JOIN ", entityName, " E on E.ID=T1.entityId");

	set @sql3 = " WHERE T1.entityListId=?
				and T2.tenantid=?;";

	set @sql4 = concat(@sql1,@sql2,@sql3);
	set @id = _id;
    set @tenantid=_tenantid;

	prepare stmt from @sql4;
    
    execute stmt using @id,@tenantid;

	/*select @id,@tenantid,@sql4;*/

END$$

DELIMITER ;

USE `food`;
CREATE TABLE IF NOT EXISTS `productCollection`(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `userid` int(11) NOT NULL,
     `name` varchar(200) NOT NULL,
     `description` text,
     `introText` text,
     `queryParams` varchar(200),
     `imageUrl` varchar(200),
     PRIMARY KEY (`id`),
     KEY `fk_productCollection_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_productCollection_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

USE `food`;
DROP procedure IF EXISTS `getProductCollectionById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getProductCollectionById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT id,
          name,
          description,
          introText,
          queryParams,
          imageUrl
     FROM
          productCollection
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getProductCollections`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getProductCollections`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT id,
          name,
          description,
          introText,
          queryParams,
          imageUrl
     FROM
          productCollection
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
DROP procedure IF EXISTS `addProductCollection`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addProductCollection(name varchar(200), description text, introText text, queryParams varchar(200), imageUrl varchar(200), tenantid int, userid int)
BEGIN

     INSERT INTO productCollection(
          name,
          description,
          introText,
          queryParams,
          imageUrl,
          tenantid,
          userid)
     VALUES (name,
          description,
          introText,
          queryParams,
          imageUrl,
          tenantid,
          userid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateProductCollection`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updateProductCollection(_id int, name varchar(200), description text, introText text, queryParams varchar(200), imageUrl varchar(200), _tenantid int, _userid int)
BEGIN

     UPDATE productCollection SET
          name = name,
          description = description,
          introText = introText,
          queryParams = queryParams,
          imageUrl = imageUrl
     WHERE
          id=_id
          AND tenantid=_tenantid
          AND userid=_userid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deleteProductCollection`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE deleteProductCollection(id int, tenant int, userid int)
BEGIN

     DELETE FROM productCollection WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateTenantContent`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateTenantContent`(_id int, name varchar(100), contentText text, language varchar(10), _tenantid int)
BEGIN

     UPDATE tenantContent SET
          name = name,
          contentText = contentText,
          language = language
     WHERE
          id=_id
          AND tenantid=_tenantid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `removeTenantSettings`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTenantSettings`(targetTenantId int, callingTenantId int)
BEGIN

	delete from tenantSetting where tenantid=targetTenantId;


END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `removeTenantProperties`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTenantProperties`(targetTenantId int, callingTenantId int)
BEGIN

	delete from tenantProperty where tenantid=targetTenantId;


END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `removeTenantCategories`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTenantCategories`(targetTenantId int, callingTenantId int)
BEGIN

	delete from category where tenantid=targetTenantId;


END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `removeTenantMenuItems`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTenantMenuItems`(targetTenantId int, callingTenantId int)
BEGIN

	delete from menuItem where tenantid=targetTenantId;


END$$
DELIMITER ;


USE `food`;
DROP procedure IF EXISTS `getPagesForTenantByCollection`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPagesForTenantByCollection`(_collectionName varchar(100), _tenantid int)
BEGIN

	select count(*) into @colCount from pageCollection where name=_collectionName and tenantid=_tenantid;
    
	IF @colCount=0 THEN
		insert into pageCollection(name, tenantid) values (_collectionName, _tenantid);
    END IF;

	select P.id, P.name, P.shortdesc,P.url, P.imageurl, P.roles 
	from
		pageCollectionItem PCI
		inner join pageCollection PC on PC.id=PCI.pageCollectionId
		inner join page P on P.id=PCI.pageId
    where
		PC.tenantid=_tenantid
        and PC.name=_collectionName
	order by PCI.seq;
    

END$$
DELIMITER ;

USE `food`;
CREATE TABLE IF NOT EXISTS `propertyBag`(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `tenantid` int(11) NOT NULL,
     `name` varchar(300) NOT NULL,
     `properties` text,
     PRIMARY KEY (`id`),
     KEY `fk_propertyBag_tenant_idx` (`tenantid`),
     CONSTRAINT `fk_propertyBag_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

USE `food`;
DROP procedure IF EXISTS `getPropertyBagById`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getPropertyBagById`(_id int, _tenantid int, _userid int)
BEGIN

     SELECT id,
          name,
          properties
     FROM
          propertyBag
      WHERE
          id=_id AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getPropertyBagByName`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getPropertyBagByName`(_name varchar(300), _tenantid int, _userid int)
BEGIN

     SELECT id,
          name,
          properties
     FROM
          propertyBag
      WHERE
          name=_name AND tenantid=_tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getPropertyBags`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE `getPropertyBags`(userid int, numToReturn int,startAt int,tenantid int)
BEGIN

     prepare stmt from "SELECT id,
          name,
          properties
     FROM
          propertyBag
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
DROP procedure IF EXISTS `addPropertyBag`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE addPropertyBag(name varchar(300), properties text, tenantid int)
BEGIN

     INSERT INTO propertyBag(
          name,
          properties,
          tenantid)
     VALUES (name,
          properties,
          tenantid);

     SELECT Last_Insert_ID() as newID;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updatePropertyBag`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE updatePropertyBag(_id int, name varchar(300), properties text, _tenantid int)
BEGIN

     UPDATE propertyBag SET
          name = name,
          properties = properties
     WHERE
          id=_id
          AND tenantid=_tenantid;
END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `deletePropertyBag`;

DELIMITER $$
USE `food`$$

CREATE PROCEDURE deletePropertyBag(id int, tenant int, userid int)
BEGIN

     DELETE FROM propertyBag WHERE id=id AND tenantid=tenantid;

END$$
DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `getLocationsBySearchCriteria`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLocationsBySearchCriteria`(tenantid int, _name varchar(100), state varchar(10), numToReturn integer, startAt integer)
BEGIN

set @searchCriteria = '';

IF LENGTH(_name)>0 THEN
		set @searchCriteria = concat("and L.name like ","'",_name,"%'");
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
DROP procedure IF EXISTS `getProductsByEntityListIdEx`;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductsByEntityListIdEx`(_id int, _tenantid int,_start int, _return int)
BEGIN

	prepare stmt from "SELECT
          P.id,
          P.name,
          P.url,
          P.title,
          P.author,
          P.description,
          P.price,
          P.url,
          P.imageUrl
		FROM
          entityListItem T1
          INNER JOIN entityList T2 ON T2.id=T1.entityListId
          LEFT JOIN product P on P.ID=T1.entityId
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

USE food;

CREATE TABLE IF NOT EXISTS `food`.`event` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `event` VARCHAR(100) NULL,
  `entityType` VARCHAR(100) NULL,
  `entityId` INT NULL,
  `datetime` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` INT NULL,
  `sessionId` VARCHAR(100) NULL,
  `tenantId` INT NULL,
  PRIMARY KEY (`id`));



