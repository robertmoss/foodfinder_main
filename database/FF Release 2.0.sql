USE `food`;
DROP procedure IF EXISTS `updateUser`;

DELIMITER $$
USE `food`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUser`(_id int, _name varchar(100), email varchar(300), _password varchar(300), bio text, tenantid int)
BEGIN

	update user set
		name = _name,
		email = email,
        bio=bio
	where
		id = _id;


END$$

DELIMITER ;

USE `food`;
DROP procedure IF EXISTS `updateTenant`;

DELIMITER $$
USE `food`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateTenant`(_id int, _name varchar(100), _title varchar(100), _welcome text,  _css varchar(1000), _allowAnonAccess bit,_tenantid int)
BEGIN

	update tenant set
		name = _name,
        title = _title,
        welcome = _welcome,
        css = _css,
        allowAnonAccess = _allowAnonAccess
	where
		id = _id;

END$$

DELIMITER ;

USE `food`;
DROP procedure IF EXISTS getPagesForTenantByCollection;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPagesForTenantByCollection`(_collectionName varchar(100), _tenantid int)
BEGIN

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

insert into pageCollection(name,tenantid)
select 'home',id from tenant where id not in 
(select T.id from 
	tenant T
    left outer join pageCollection PC on PC.tenantid=T.id
    where PC.name='home');

update application set databaseRevision='2.0.2' where id=1;

