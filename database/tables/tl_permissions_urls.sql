/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tl_permissions_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `tl_permissions_urls` VALUES (1,5,'/create/home'),(2,5,'/edit/home'),(3,5,'/manage/files'),(4,5,'/manage/files/bucket'),(5,5,'/manage/files/change_acl'),(6,5,'/manage/files/delete_bucket'),(7,5,'/manage/files/delete_object'),(8,5,'/manage/files/output_object'),(9,5,'/manage/home'),(10,5,'/manage/import/authorimport'),(11,5,'/manage/import/columnimport'),(12,5,'/manage/import/import_authorphotos'),(13,5,'/manage/import/import_photos'),(14,5,'/manage/import/photoimport'),(15,5,'/manage/import/tdmimport'),(16,5,'/manage/sections'),(17,5,'/manage/sections/dosave'),(18,5,'/manage/sections/section'),(19,5,'/manage/users'),(20,5,'/manage/users/accounts'),(21,5,'/manage/users/add'),(22,5,'/manage/users/edit'),(23,5,'/manage/users/my_account'),(24,5,'/manage/users/permissions'),(25,5,'/publish/home'),(26,4,'/edit/home'),(27,4,'/manage/files'),(28,4,'/manage/files/bucket'),(29,4,'/manage/files/change_acl'),(30,4,'/manage/files/delete_bucket'),(31,4,'/manage/files/delete_object'),(32,4,'/manage/files/output_object'),(33,4,'/manage/users'),(34,4,'/manage/users/accounts'),(35,4,'/manage/users/add'),(36,4,'/manage/users/edit'),(37,4,'/manage/users/permissions'),(38,4,'/publish/home'),(39,3,'/manage/files'),(40,3,'/manage/files/bucket'),(41,3,'/manage/files/change_acl'),(42,3,'/manage/files/delete_bucket'),(43,3,'/manage/files/delete_object'),(44,3,'/manage/files/output_object'),(45,3,'/manage/home'),(46,3,'/manage/users'),(47,3,'/manage/users/accounts'),(48,3,'/manage/users/add'),(49,3,'/manage/users/edit'),(50,3,'/manage/users/permissions'),(51,3,'/publish/home');
