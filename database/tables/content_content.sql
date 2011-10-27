/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content_link_id` int(11) NOT NULL,
  `fieldname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_id` (`content_id`,`content_link_id`)
) ENGINE=MyISAM AUTO_INCREMENT=505 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `content_content` VALUES (492,347,327,'mainpic'),(2,60,59,''),(24,80,79,''),(491,350,352,''),(264,100,98,''),(449,98,310,''),(480,312,341,''),(450,98,311,''),(483,323,339,'mainpic'),(486,325,340,'mainpic'),(454,309,326,''),(488,332,346,'mainpic'),(489,334,336,'mainpic'),(484,337,338,'mainpic'),(493,348,328,'mainpic');
