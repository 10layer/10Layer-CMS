/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platforms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `urlid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `base_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified` datetime NOT NULL,
  `type_id` int(11) NOT NULL,
  `publication_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlid` (`urlid`),
  KEY `publication_id` (`publication_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `platforms` VALUES (1,'Web','web','www.10layer.com','2011-01-17 11:27:00','2011-01-17 13:27:00',1,1),(2,'Twitter','twitter','twitter.com/10layer','2011-01-17 11:27:00','2011-01-17 13:27:00',2,1),(4,'Web','fam-web','http://www.freeafricanmedia.com','2011-05-09 08:34:19','0000-00-00 00:00:00',1,2);
