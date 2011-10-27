/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `caption` text COLLATE utf8_unicode_ci NOT NULL,
  `effects` text COLLATE utf8_unicode_ci NOT NULL,
  `cdn_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `pictures` VALUES (68,292,'/resources/uploads/files/original/2011/06/28/photo.jpg','','',''),(69,293,'/resources/uploads/files/original/2011/06/28/photo.jpg','','',''),(70,294,'/resources/uploads/files/original/2011/06/28/photo.jpg','','',''),(66,90,'/resources/uploads/files/original/2011/06/28/photo.jpg','','',''),(67,291,'/resources/uploads/files/original/2011/06/28/photo.jpg','','',''),(65,89,'/resources/uploads/files/original/2011/06/28/photo.jpg','','',''),(64,87,'/resources/uploads/files/original/2011/06/27/btn_submit.png','','',''),(76,322,'/resources/uploads/files/original/2011/10/21/Screen_shot_2011-09-08_at_3.37_.44_PM_.png','','','http://c714723.r23.cf2.rackcdn.com/Screen_shot_2011-09-08_at_3.37_.44_PM_.png'),(62,85,'/resources/uploads/files/original/2011/06/27/ico_approvedeny.png','','',''),(61,84,'/resources/uploads/files/original/2011/06/27/folder.png','','',''),(58,81,'/resources/uploads/files/original/2011/06/27/logo.png','','',''),(59,82,'/resources/uploads/files/original/2011/06/27/versions.png','','',''),(60,83,'/resources/uploads/files/original/2011/06/27/logo-convene.png','','',''),(77,324,'/resources/uploads/files/original/2011/10/21/ipads.png','','','http://c714723.r23.cf2.rackcdn.com/ipads.png'),(75,319,'','','',''),(78,327,'/resources/uploads/files/original/2011/10/21/jason.png','','','http://c714723.r23.cf2.rackcdn.com/jason.png'),(79,328,'/resources/uploads/files/original/2011/10/21/guy.png','','','http://c714723.r23.cf2.rackcdn.com/guy.png'),(80,333,'/resources/uploads/files/original/2011/10/21/team1.png','','','http://c714723.r23.cf2.rackcdn.com/team1.png'),(81,336,'/resources/uploads/files/original/2011/10/21/team2.png','','','http://c714723.r23.cf2.rackcdn.com/team2.png'),(82,338,'/resources/uploads/files/original/2011/10/21/team_deepetch.png','','','http://c714723.r23.cf2.rackcdn.com/team_deepetch.png'),(83,339,'/resources/uploads/files/original/2011/10/21/screenshots_deepetch.png','','','http://c714723.r23.cf2.rackcdn.com/screenshots_deepetch.png'),(84,340,'/resources/uploads/files/original/2011/10/21/imaverick_deepetch_half.png','','','http://c714723.r23.cf2.rackcdn.com/imaverick_deepetch_half.png'),(85,346,'/resources/uploads/files/original/2011/10/24/logo_white.png','','','http://c714723.r23.cf2.rackcdn.com/logo_white.png');
