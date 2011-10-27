/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlid` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `table_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `model` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `contenttype` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `collection` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlid` (`urlid`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `content_types` VALUES (1,'article','Article',1,'articles','Model_Articles','',0),(2,'picture','Picture',1,'pictures','Model_Pictures','image.*',0),(3,'page','Page',1,'pages','Model_Pages','',0),(10,'section','Sections',1,'site_sections','Model_Site_Sections','',1),(11,'zones','Zones',0,'section_zones','Model_Zones','',0),(16,'author','Author',1,'authors','Model_Authors','',0),(17,'tag','Tag',1,'tag','Model_Tag','',0);
