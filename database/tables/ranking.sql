/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `zone_urlid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rank` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zone_urlid` (`zone_urlid`),
  KEY `content_urlid` (`content_id`),
  KEY `rank` (`rank`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `ranking` VALUES (18,72,'2011-09-02-home-zone-1',1),(16,89,'2011-09-02-home-zone-2',0),(17,70,'2011-09-02-home-zone-1',0),(11,70,'2011-08-28-africa-zone-1',0),(12,72,'2011-08-28-africa-zone-1',1),(13,88,'2011-09-06-africa-right-column',0),(14,72,'2011-09-06-africa-left-column',0),(19,319,'2011-09-08-picture',0),(21,319,'home-picture',0),(41,319,'home_picture',1),(40,89,'home_picture',0),(38,74,'2011-09-08-content',2),(37,65,'2011-09-08-content',1),(36,70,'2011-09-08-content',0),(39,73,'2011-09-08-content',3),(54,332,'products-zone-1',3),(43,68,'2011-09-08-blog-zone-1',0),(44,70,'2011-09-08-blog-zone-1',1),(53,325,'products-zone-1',2),(52,337,'products-zone-1',1),(51,323,'products-zone-1',0),(55,334,'products-zone-1',4),(73,70,'main-blog-zone',3),(72,71,'main-blog-zone',2),(71,68,'main-blog-zone',1),(63,348,'team-zone-1',1),(62,347,'team-zone-1',0),(64,349,'team-zone-1',2),(74,72,'main-blog-zone',4),(70,69,'main-blog-zone',0),(75,67,'main-blog-zone',5),(76,66,'main-blog-zone',6),(77,65,'main-blog-zone',7);
