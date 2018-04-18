--
-- Disable foreign keys
--
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

--
-- Set SQL mode
--
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

--
-- Set character set the client will use to send SQL statements to the server
--
SET NAMES 'utf8';

--
-- Set default database
--
USE crontabmanager;

--
-- Drop table `crontab_to_server`
--
DROP TABLE IF EXISTS crontab_to_server;

--
-- Drop table `crontab_groups`
--
DROP TABLE IF EXISTS crontab_groups;

--
-- Drop table `nrpe_to_server`
--
DROP TABLE IF EXISTS nrpe_to_server;

--
-- Drop table `server`
--
DROP TABLE IF EXISTS server;

--
-- Set default database
--
USE crontabmanager;

--
-- Create table `server`
--
CREATE TABLE server (
  ser_id int(11) NOT NULL AUTO_INCREMENT,
  ser_ip char(15) NOT NULL,
  ser_descr varchar(255) DEFAULT NULL,
  ser_active tinyint(1) NOT NULL DEFAULT 1,
  ser_db_created datetime DEFAULT NULL,
  ser_db_changed datetime DEFAULT NULL,
  PRIMARY KEY (ser_id)
)
  ENGINE = INNODB,
  AUTO_INCREMENT = 1,
  AVG_ROW_LENGTH = 1024,
  CHARACTER SET utf8,
  COLLATE utf8_general_ci;

--
-- Create index `IX_server_ser_ip` on table `server`
--
ALTER TABLE server
  ADD INDEX IX_server_ser_ip (ser_ip);

--
-- Create index `UK_server_ser_descr` on table `server`
--
ALTER TABLE server
  ADD UNIQUE INDEX UK_server_ser_descr (ser_descr);

DELIMITER $$

--
-- Create trigger `tr_ser_bi`
--
CREATE
  DEFINER = 'cronmanager'@'localhost'
TRIGGER tr_ser_bi
  BEFORE INSERT
  ON server
  FOR EACH ROW
  BEGIN
    SET NEW.ser_db_created = NOW();
  END
$$

--
-- Create trigger `tr_ser_bu`
--
CREATE
  DEFINER = 'cronmanager'@'localhost'
TRIGGER tr_ser_bu
  BEFORE UPDATE
  ON server
  FOR EACH ROW
  BEGIN
    SET NEW.ser_db_changed = NOW();
  END
$$

DELIMITER ;

--
-- Create table `nrpe_to_server`
--
CREATE TABLE nrpe_to_server (
  nrp_id int(11) NOT NULL AUTO_INCREMENT,
  nrp_command_name varchar(255) NOT NULL,
  nrp_command varchar(255) DEFAULT NULL,
  nrp_ser_id int(11) DEFAULT NULL,
  PRIMARY KEY (nrp_id)
)
  ENGINE = INNODB,
  AUTO_INCREMENT = 1,
  AVG_ROW_LENGTH = 261,
  CHARACTER SET utf8,
  COLLATE utf8_general_ci;

--
-- Create foreign key
--
ALTER TABLE nrpe_to_server
  ADD CONSTRAINT FK_nrpe_to_server_server_ser_id FOREIGN KEY (nrp_ser_id)
REFERENCES server (ser_id) ON DELETE CASCADE;

--
-- Create table `crontab_groups`
--
CREATE TABLE crontab_groups (
  crg_id int(11) NOT NULL AUTO_INCREMENT,
  crg_comment text DEFAULT NULL,
  crg_active tinyint(4) NOT NULL DEFAULT 1,
  crg_db_created datetime DEFAULT NULL,
  crg_db_changed datetime DEFAULT NULL,
  PRIMARY KEY (crg_id)
)
  ENGINE = INNODB,
  AUTO_INCREMENT = 1,
  AVG_ROW_LENGTH = 399,
  CHARACTER SET utf8,
  COLLATE utf8_general_ci;

DELIMITER $$

--
-- Create trigger `tr_crg_bi`
--
CREATE
  DEFINER = 'cronmanager'@'%'
TRIGGER tr_crg_bi
  BEFORE INSERT
  ON crontab_groups
  FOR EACH ROW
  BEGIN
    SET NEW.crg_db_created = NOW();
  END
$$

--
-- Create trigger `tr_crg_bu`
--
CREATE
  DEFINER = 'cronmanager'@'%'
TRIGGER tr_crg_bu
  BEFORE UPDATE
  ON crontab_groups
  FOR EACH ROW
  BEGIN
    SET NEW.crg_db_changed = NOW();
  END
$$

DELIMITER ;

--
-- Create table `crontab_to_server`
--
CREATE TABLE crontab_to_server (
  cro_id int(11) NOT NULL AUTO_INCREMENT,
  cro_crg_id int(11) DEFAULT NULL,
  cro_ser_id int(11) DEFAULT NULL,
  cro_active tinyint(1) NOT NULL DEFAULT 1,
  cro_descr varchar(255) DEFAULT NULL,
  cro_user varchar(255) NOT NULL,
  cro_m char(10) NOT NULL,
  cro_h char(10) NOT NULL,
  cro_dom char(10) NOT NULL,
  cro_mon char(10) NOT NULL,
  cro_dow char(10) NOT NULL,
  cro_command varchar(255) NOT NULL,
  cro_db_created datetime DEFAULT NULL,
  cro_db_changed datetime DEFAULT NULL,
  PRIMARY KEY (cro_id)
)
  ENGINE = INNODB,
  AUTO_INCREMENT = 1,
  AVG_ROW_LENGTH = 544,
  CHARACTER SET utf8,
  COLLATE utf8_general_ci;

DELIMITER $$

--
-- Create trigger `tr_cro_bi`
--
CREATE
  DEFINER = 'cronmanager'@'%'
TRIGGER tr_cro_bi
  BEFORE INSERT
  ON crontab_to_server
  FOR EACH ROW
  BEGIN
    SET new.cro_db_created = NOW();
  END
$$

DELIMITER ;

--
-- Create foreign key
--
ALTER TABLE crontab_to_server
  ADD CONSTRAINT FK_crontab_to_server_crontab_groups_crg_id FOREIGN KEY (cro_crg_id)
REFERENCES crontab_groups (crg_id);

--
-- Create foreign key
--
ALTER TABLE crontab_to_server
  ADD CONSTRAINT FK_crontab_zu_server_server_ser_id FOREIGN KEY (cro_ser_id)
REFERENCES server (ser_id) ON DELETE CASCADE;

--
-- Restore previous SQL mode
--
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

--
-- Enable foreign keys
--
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;