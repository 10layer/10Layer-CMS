/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tl_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `major_version` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `urlid` (`urlid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `tl_workflows` VALUES (1,'initial_creation','Initial creation',0),(2,'subbing','Subbing',1),(3,'editting','Editing',2),(4,'queued_for_publishing','Queued for publishing',3),(5,'published','Published',4);
