/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlid` (`urlid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `tag_type` VALUES (1,'company','Dell'),(2,'currency','USD'),(3,'country','United States'),(4,'organization','World Trade Organization'),(5,'person','Obama'),(6,'city','Beijing'),(7,'industryterm','energy'),(8,'technology','X-ray'),(9,'position','journalist');
