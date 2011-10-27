/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_id` int(11) NOT NULL,
  `urlid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `blurb` text COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlid` (`urlid`),
  KEY `platform_id` (`platform_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `sections` VALUES (3,1,'politics','Politics','',2,'2011-04-20 07:35:19'),(2,1,'home','Home','',1,'2010-12-07 07:54:41'),(4,1,'business','Business','',3,'2011-04-20 07:35:19'),(5,1,'world','World','',4,'2011-04-20 07:35:44'),(6,1,'media','Media','',5,'2011-04-20 07:35:44'),(7,1,'tech','Technology','',6,'2011-04-20 07:36:19'),(8,1,'sport','Sport','',7,'2011-04-20 07:36:19'),(9,1,'opinion','Opinion','',8,'2011-04-20 07:36:47'),(10,1,'menus','Menus','The menu systems',0,'2011-06-26 19:32:53');
