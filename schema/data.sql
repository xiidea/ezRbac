/*
SQLyog Ultimate v9.62 
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

insert  into `system_users`(`id`,`email`,`password`,`salt`,`user_role_id`,`last_login`,`last_login_ip`,`reset_request_code`,`reset_request_time`,`reset_request_ip`,`verification_status`) values (1,'admin@admin.com','8e666f12a66c17a952a1d5e273428e478e02d943','4f6cdddc4979b8.51434094',1,'2012-03-24 02:52:51',0,NULL,NULL,NULL,1);

/*Data for the table `user_access_map` */

insert  into `user_access_map`(`user_role_id`,`controller`,`permission`) values (1,'admin/welcome',1);

/*Data for the table `user_autologin` */

/*Data for the table `user_role` */

insert  into `user_role`(`id`,`role_name`,`default_access`) values (1,'Admin','11111');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
