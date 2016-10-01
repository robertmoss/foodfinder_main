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
