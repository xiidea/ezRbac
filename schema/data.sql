/*
SQLyog Ultimate v9.50 
MySQL - 5.1.41 : Database - ezrbac
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `system_users` */

insert  into `system_users`(`id`,`email`,`password`,`salt`,`user_role_id`,`last_login`,`last_login_ip`,`reset_request_code`,`reset_request_time`,`reset_request_expiry`,`reset_request_ip`,`new_email`,`new_password`,`verification_status`) values (1,'a@a.com','71dd07494c5ee54992a27746d547e25dee01bd97','123456',1,'2012-03-12 10:43:40',0,NULL,NULL,NULL,NULL,NULL,NULL,1);

/*Data for the table `user_access_map` */

insert  into `user_access_map`(`user_role_id`,`controller`,`permission`) values (1,'admin/welcome','00000');

/*Data for the table `user_autologin` */

/*Data for the table `user_role` */

insert  into `user_role`(`id`,`role_name`,`default_access`) values (1,'Admin','11111');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
