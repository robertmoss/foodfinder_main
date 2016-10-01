

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
        set @fieldText = ",concat(E.name,"" ("", E.city,E.state, "")"" as displayText";
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


