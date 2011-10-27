/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content_types` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL DEFAULT '100',
  `max_count` int(11) NOT NULL,
  `min_count` int(11) NOT NULL,
  `auto_where` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_limit` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_order_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_join_table` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_join_direction` tinyint(1) NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1292 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `section_zones` VALUES (1288,326,'product',0,0,0,'','','','',0,0),(1289,341,'article',0,0,0,'','','','',0,0),(1291,352,'team',0,0,0,'','','','',0,0);
