/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subsections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `section_id` int(11) NOT NULL,
  `content_types` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `max_count` int(11) NOT NULL,
  `min_count` int(11) NOT NULL,
  `auto_where` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_limit` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_order_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlid` (`urlid`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `subsections` VALUES (1,'home_main','Main Section',2,'article',1,100,4,'','','',0),(2,'home_columnists','Columnists',2,'column',2,100,1,'','','',0),(3,'politics-main','Main Section',3,'article',1,100,5,'','','',0),(4,'business-main','Main Section',4,'article',1,100,5,'','','',0),(5,'world-main','Main Section',5,'article',1,100,5,'','','',0),(6,'media-main','Main Section',6,'article',1,100,5,'','','',0),(7,'tech-main','Main Section',7,'article',1,100,5,'','','',0),(8,'sport-main','Main Section',8,'article',1,100,5,'','','',0),(9,'opinionistas-main','Main Section',9,'author',1,100,5,'','','',0),(10,'menu-top','Top Menu',10,'page',1,10,1,'','','',0);
