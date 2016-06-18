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

