/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي للسجل',
  `log_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? اسم السجل أو الوحدة مثل: users, orders, products, auth',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? وصف النشاط المنفذ (مثلاً: تعديل حالة الطلب، حذف منتج...)',
  `event` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? نوع الحدث: created / updated / deleted / login / approval / custom',
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL COMMENT '? خصائص إضافية أو تفاصيل العملية بصيغة JSON',
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? UUID لتجميع الأنشطة المترابطة (Batch Actions)',
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '?️ الوحدة أو القسم: Users / Orders / Suppliers / Auth / Products',
  `action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '⚙️ نوع العملية داخل الوحدة مثل: login, approve, verify, print',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? عنوان IP للمستخدم المنفّذ للعملية',
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? معلومات الجهاز أو المتصفح المستخدم في العملية',
  `platform` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? نوع النظام أو الجهاز المستخدم (Web / Mobile / API)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ حذف ناعم للسجل دون فقد البيانات',
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_event_index` (`log_name`,`event`,`created_at`),
  KEY `activity_module_action_index` (`module`,`action`),
  KEY `activity_user_subject_index` (`causer_id`,`subject_id`),
  KEY `activity_log_log_name_index` (`log_name`),
  KEY `activity_log_event_index` (`event`),
  KEY `activity_log_batch_uuid_index` (`batch_uuid`),
  KEY `activity_log_module_index` (`module`),
  KEY `activity_log_action_index` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `buyers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buyers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'المعرّف الأساسي للمشتري',
  `user_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `organization_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم المنشأة مثل: مستشفى السلام أو مختبر النور',
  `organization_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'نوع المنشأة: مستشفى / عيادة / مختبر / مركز طبي',
  `license_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'رقم الترخيص الصحي للمنشأة',
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'بلد المشتري',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'المدينة التي تقع فيها المنشأة',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'العنوان الكامل للمشتري',
  `contact_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'بريد إلكتروني خاص بالتواصل التجاري',
  `contact_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'رقم الهاتف التجاري الرئيسي',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'هل تم التحقق من المشتري من قِبل إدارة المنصة؟',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ اعتماد المشتري بعد المراجعة',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'هل الحساب نشط ويمكن للمشتري الدخول؟',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'سبب رفض طلب التسجيل (إن وُجد)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  KEY `buyers_created_by_foreign` (`created_by`),
  KEY `buyers_updated_by_foreign` (`updated_by`),
  KEY `buyer_search_index` (`organization_name`,`country`,`city`,`is_verified`),
  KEY `buyers_user_id_is_verified_index` (`user_id`,`is_verified`),
  KEY `buyers_is_verified_index` (`is_verified`),
  KEY `buyers_is_active_index` (`is_active`),
  CONSTRAINT `buyers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `buyers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `buyers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'مفتاح الكاش (PK)',
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'قيمة الكاش (مسلسلة/مشفّرة)',
  `expiration` int DEFAULT NULL COMMENT 'وقت انتهاء الكاش (unix timestamp) - nullable للدلالات الخاصة',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'مفتاح القفل (PK)',
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'المالك/الجهاز الذي خلق القفل',
  `expiration` int NOT NULL COMMENT 'وقت انتهاء القفل (unix timestamp)',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي لعملية التسليم',
  `order_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `delivery_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? رقم التوصيل الفريد داخل النظام',
  `delivery_date` timestamp NULL DEFAULT NULL COMMENT '? تاريخ التسليم الفعلي',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT '⏱️ وقت تأكيد عملية التسليم فعليًا',
  `status` enum('pending','in_transit','delivered','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '? حالة التسليم: قيد الانتظار / في الطريق / تم التسليم / فشل',
  `delivery_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? موقع التسليم الفعلي (إحداثيات أو عنوان)',
  `receiver_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? اسم الشخص المستلم',
  `receiver_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? رقم هاتف المستلم',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '? هل تم تأكيد عملية التسليم من قبل المستلم؟',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT '?️ ملاحظات إضافية حول التسليم',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ حذف منطقي دون فقدان البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `deliveries_delivery_number_unique` (`delivery_number`),
  KEY `deliveries_buyer_id_foreign` (`buyer_id`),
  KEY `deliveries_created_by_foreign` (`created_by`),
  KEY `deliveries_verified_by_foreign` (`verified_by`),
  KEY `delivery_index` (`order_id`,`status`,`delivery_date`),
  KEY `delivery_status_index` (`supplier_id`,`buyer_id`,`status`),
  KEY `delivery_composite_index` (`order_id`,`supplier_id`,`buyer_id`,`status`),
  CONSTRAINT `deliveries_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `deliveries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `deliveries_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `deliveries_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `deliveries_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK: معرف سجل الفشل',
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UUID فريد لسجل الفشل',
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم الاتصال/driver المستخدم',
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم الطابور',
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'البيانات المرسلة للمهمة',
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'نص الاستثناء أو الخطأ',
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'وقت الفشل',
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'المعرّف الأساسي للفاتورة',
  `order_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'رقم الفاتورة الفريد',
  `invoice_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ إنشاء الفاتورة',
  `subtotal` decimal(12,2) NOT NULL COMMENT 'المجموع قبل الضرائب والخصومات (دقة مالية عالية)',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'قيمة الضريبة المضافة (إن وُجدت)',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'قيمة الخصم (إن وُجد)',
  `total_amount` decimal(12,2) NOT NULL COMMENT 'المجموع النهائي بعد الخصومات والضرائب',
  `status` enum('draft','issued','approved','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'issued' COMMENT 'حالة الفاتورة: مسودة / صادرة / معتمدة / ملغاة',
  `payment_status` enum('unpaid','partial','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid' COMMENT 'الحالة المالية الإجمالية بناءً على عمليات الدفع المرتبطة',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'ملاحظات إضافية حول الفاتورة أو الدفع',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'حذف منطقي دون فقدان البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_approved_by_foreign` (`approved_by`),
  KEY `invoice_index` (`order_id`,`status`,`payment_status`,`invoice_date`),
  CONSTRAINT `invoices_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'معرف الباتش (UUID/string) - PK',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم الباتش',
  `total_jobs` int NOT NULL COMMENT 'إجمالي المهام في الباتش',
  `pending_jobs` int NOT NULL COMMENT 'المهام المعلقة',
  `failed_jobs` int NOT NULL COMMENT 'المهام الفاشلة',
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'قائمة معرفات المهام الفاشلة (serialized)',
  `options` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'خيارات الباتش (serialized/json)',
  `cancelled_at` int DEFAULT NULL COMMENT 'وقت الإلغاء (unix timestamp)',
  `created_at` int NOT NULL COMMENT 'وقت الإنشاء (unix timestamp)',
  `finished_at` int DEFAULT NULL COMMENT 'وقت الانتهاء (unix timestamp)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK: معرف المهمة',
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم طابور المهمة',
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'البيانات المسلسلة للمهمة',
  `attempts` tinyint unsigned NOT NULL COMMENT 'عدد المحاولات السابقة',
  `reserved_at` int unsigned DEFAULT NULL COMMENT 'وقت الحجز (unix timestamp)',
  `available_at` int unsigned NOT NULL COMMENT 'وقت توافر التنفيذ (unix timestamp)',
  `created_at` int unsigned NOT NULL COMMENT 'وقت الإنشاء (unix timestamp)',
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'UUID اختياري للربط الخارجي',
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم مجموعة الميديا',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم العنصر داخل المجموعة',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم الملف على القرص',
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'نوع MIME',
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'القرص/الـ storage disk المستخدم',
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'قرص التحويلات إن وُجد',
  `size` bigint unsigned NOT NULL COMMENT 'حجم الملف بالبايت',
  `manipulations` json DEFAULT NULL COMMENT 'الإجراءات/التعديلات (json)',
  `custom_properties` json DEFAULT NULL COMMENT 'خصائص مخصصة (json)',
  `generated_conversions` json DEFAULT NULL COMMENT 'التحويلات المولّدة (json)',
  `responsive_images` json DEFAULT NULL COMMENT 'صور responsive (json)',
  `order_column` int unsigned DEFAULT NULL COMMENT 'ترتيب الملف داخل المجموعة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL COMMENT 'FK: permission id',
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'model class (morph)',
  `model_id` bigint unsigned NOT NULL COMMENT 'model id (morph)',
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL COMMENT 'FK: role id',
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'model class (morph)',
  `model_id` bigint unsigned NOT NULL COMMENT 'model id (morph)',
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `quotation_item_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `item_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specifications` text COLLATE utf8mb4_unicode_ci,
  `quantity` int NOT NULL DEFAULT '1',
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(12,2) NOT NULL,
  `lead_time` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','confirmed','in_production','ready_to_ship','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_order_id_product_id_index` (`order_id`,`product_id`),
  KEY `order_items_order_id_status_index` (`order_id`,`status`),
  KEY `order_items_quotation_item_id_index` (`quotation_item_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_items_quotation_item_id_foreign` FOREIGN KEY (`quotation_item_id`) REFERENCES `quotation_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'المعرّف الأساسي لأمر الشراء',
  `quotation_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `order_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'رقم فريد لتعريف أمر الشراء',
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ إنشاء الطلب',
  `status` enum('pending','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'حالة الطلب: قيد الانتظار / جاري التنفيذ / تم الشحن / تم التسليم / ملغي',
  `total_amount` decimal(12,2) NOT NULL COMMENT 'القيمة الإجمالية لأمر الشراء (دقة مالية عالية)',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LYD' COMMENT 'العملة المستخدمة في الطلب',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'ملاحظات إضافية حول الطلب',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_quotation_id_foreign` (`quotation_id`),
  KEY `orders_supplier_id_foreign` (`supplier_id`),
  KEY `orders_created_by_foreign` (`created_by`),
  KEY `order_management_index` (`buyer_id`,`supplier_id`,`status`),
  KEY `order_number_index` (`order_number`),
  CONSTRAINT `orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'البريد الإلكتروني المرتبط برمز الاسترجاع (PK)',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'توكن إعادة تعيين كلمة المرور',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'وقت إنشاء التوكن',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي لعملية الدفع',
  `invoice_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `buyer_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `processed_by` bigint unsigned DEFAULT NULL,
  `payment_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? معرّف مرجعي داخلي لعملية الدفع',
  `amount` decimal(12,2) NOT NULL COMMENT '? قيمة المبلغ المدفوع بدقة مالية عالية',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LYD' COMMENT '? العملة المستخدمة في الدفع (الدينار الليبي افتراضياً)',
  `method` enum('cash','bank_transfer','credit_card','paypal','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash' COMMENT '? طريقة الدفع (نقدي، تحويل بنكي، بطاقة، بايبال...)',
  `payment_type` enum('advance','final','refund') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? نوع الدفع: دفعة مقدمة / نهائية / استرجاع',
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? رقم العملية البنكية أو المرجع المالي الخارجي',
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '? حالة الدفع (قيد الانتظار / مكتمل / فشل / تم الاسترجاع)',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT '? ملاحظات إضافية حول عملية الدفع',
  `paid_at` timestamp NULL DEFAULT NULL COMMENT '? تاريخ تنفيذ الدفع',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_reference_unique` (`payment_reference`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_supplier_id_foreign` (`supplier_id`),
  KEY `payments_processed_by_foreign` (`processed_by`),
  KEY `payment_status_index` (`status`,`paid_at`),
  KEY `payment_party_index` (`buyer_id`,`supplier_id`,`status`),
  CONSTRAINT `payments_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payments_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم الصلاحية (مثال: manage-users)',
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'guard name (web/api)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'حذف ناعم لصلاحيات إن احتجنا',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'PK: معرف التوكن',
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم التوكن أو وصفه',
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'قيمة التوكن (مخزّن بشكل آمن)',
  `abilities` text COLLATE utf8mb4_unicode_ci COMMENT 'القدرات الممنوحة للتوكن (json/text)',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'آخر استخدام للتوكن',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ انتهاء صلاحية التوكن',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي للفئة',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? اسم الفئة بالإنجليزية (مثل: Medical Imaging)',
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? اسم الفئة بالعربية (مثل: التصوير الطبي)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? معرّف URL فريد (مثل: medical-imaging)',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '? وصف تفصيلي للفئة ومحتوياتها',
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '✅ حالة الفئة: نشطة / غير نشطة',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT '? ترتيب العرض (الأصغر يظهر أولاً)',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_categories_slug_unique` (`slug`),
  KEY `product_categories_created_by_foreign` (`created_by`),
  KEY `product_categories_updated_by_foreign` (`updated_by`),
  KEY `category_hierarchy_index` (`parent_id`,`is_active`,`sort_order`),
  CONSTRAINT `product_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_supplier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT ' المعرّف الأساسي للسجل',
  `product_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '? السعر الذي يقدمه المورد للمنتج',
  `stock_quantity` int unsigned NOT NULL DEFAULT '0' COMMENT '? الكمية المتوفرة لدى المورد من هذا المنتج',
  `lead_time` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT ' مدة التوصيل أو التسليم المتوقعة (مثلاً: 3 أيام - أسبوع)',
  `status` enum('available','out_of_stock','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available' COMMENT '✅ حالة توفر المنتج لدى المورد',
  `warranty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '?️ مدة الضمان أو الشروط الخاصة للمورد',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT '? ملاحظات إضافية من المورد حول المنتج',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_supplier` (`product_id`,`supplier_id`),
  KEY `supplier_status_index` (`supplier_id`,`status`),
  KEY `product_price_index` (`product_id`,`price`),
  CONSTRAINT `product_supplier_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي للمنتج',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? اسم المنتج الطبي مثل: جهاز تخدير، مضخة حقن، إلخ',
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? رقم أو موديل المنتج',
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '?️ العلامة التجارية للمنتج',
  `category_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '? وصف تفصيلي للمنتج ومواصفاته التقنية',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' حالة المنتج: متاح / غير متاح للعرض',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  KEY `products_created_by_foreign` (`created_by`),
  KEY `products_updated_by_foreign` (`updated_by`),
  KEY `product_search_index` (`name`,`brand`),
  KEY `product_category_index` (`category_id`,`is_active`),
  KEY `products_is_active_index` (`is_active`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quotation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotation_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `rfq_item_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `item_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specifications` text COLLATE utf8mb4_unicode_ci,
  `quantity` int NOT NULL DEFAULT '1',
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `lead_time` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_items_rfq_item_id_foreign` (`rfq_item_id`),
  KEY `quotation_items_product_id_foreign` (`product_id`),
  KEY `quotation_items_quotation_id_rfq_item_id_index` (`quotation_id`,`rfq_item_id`),
  KEY `quotation_items_quotation_id_product_id_index` (`quotation_id`,`product_id`),
  CONSTRAINT `quotation_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotation_items_rfq_item_id_foreign` FOREIGN KEY (`rfq_item_id`) REFERENCES `rfq_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'المعرّف الأساسي لعرض السعر',
  `rfq_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `reference_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'كود مرجعي فريد لتعقّب عرض السعر',
  `total_price` decimal(12,2) NOT NULL COMMENT 'السعر الإجمالي المقترح للعرض (دقة مالية عالية)',
  `terms` text COLLATE utf8mb4_unicode_ci COMMENT 'شروط الدفع والتسليم الخاصة بالعرض',
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'حالة العرض: قيد الانتظار / مقبول / مرفوض',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'ملاحظات داخلية أو تعليقات على العرض',
  `valid_until` timestamp NULL DEFAULT NULL COMMENT 'تاريخ انتهاء صلاحية العرض',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_reference_code_unique` (`reference_code`),
  KEY `quotations_supplier_id_foreign` (`supplier_id`),
  KEY `quotations_created_by_foreign` (`created_by`),
  KEY `quotation_management_index` (`rfq_id`,`supplier_id`,`status`),
  CONSTRAINT `quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_rfq_id_foreign` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotations_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rfq_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfq_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي للبند داخل الطلب',
  `rfq_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `item_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? اسم المنتج المطلوب (في حال لم يكن مرتبطًا بمنتج)',
  `specifications` text COLLATE utf8mb4_unicode_ci COMMENT '? المواصفات الفنية أو التفاصيل الخاصة بالبند',
  `quantity` int NOT NULL DEFAULT '1' COMMENT '? الكمية المطلوبة من هذا المنتج',
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? وحدة القياس مثل: قطعة / كرتونة / لتر',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '✅ هل تمت الموافقة على هذا البند من قبل المشتري؟',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT '⏱️ تاريخ الموافقة (اختياري)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ حذف ناعم دون فقد البيانات',
  PRIMARY KEY (`id`),
  KEY `rfq_items_product_id_foreign` (`product_id`),
  KEY `rfq_item_lookup_index` (`rfq_id`,`product_id`,`is_approved`),
  CONSTRAINT `rfq_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rfq_items_rfq_id_foreign` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rfqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي لطلب عرض السعر',
  `buyer_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `reference_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? كود مرجعي فريد لتعقّب الطلب',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? عنوان مختصر للطلب مثل: طلب أجهزة تعقيم',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '? تفاصيل الطلب مثل المواصفات الفنية أو الكميات المطلوبة',
  `deadline` timestamp NULL DEFAULT NULL COMMENT '⏰ تاريخ انتهاء صلاحية الطلب لتقديم العروض',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT '? تاريخ الإغلاق الفعلي للطلب عند انتهاء التقديم أو الإلغاء',
  `status` enum('open','closed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open' COMMENT '? حالة الطلب: مفتوح / مغلق / ملغى',
  `is_public` tinyint(1) NOT NULL DEFAULT '1' COMMENT '? هل الطلب مرئي لجميع الموردين أم خاص؟',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rfqs_reference_code_unique` (`reference_code`),
  KEY `rfqs_created_by_foreign` (`created_by`),
  KEY `rfq_management_index` (`buyer_id`,`status`,`deadline`,`closed_at`),
  CONSTRAINT `rfqs_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rfqs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL COMMENT 'FK: permission id',
  `role_id` bigint unsigned NOT NULL COMMENT 'FK: role id',
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم الدور (مثال: Admin)',
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'guard name (web/api)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'حذف ناعم للدور إن احتجنا',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'معرف الجلسة (PK)',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'FK -> users.id (قد يكون nullable للـ guest)',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'عنوان IP المرتبط بالجلسة',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'معلومات User-Agent للمتصفح/العميل',
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'بيانات الجلسة المسلسلة',
  `last_activity` int NOT NULL COMMENT 'وقت آخر نشاط (unix timestamp)',
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي للمورد',
  `user_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `company_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? اسم الشركة أو المؤسسة التجارية للمورد',
  `commercial_register` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? رقم السجل التجاري',
  `tax_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? الرقم الضريبي (إن وجد)',
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? بلد المورد',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '?️ المدينة التي يقع فيها المورد',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? العنوان الكامل للمورد',
  `contact_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? البريد الإلكتروني للتواصل التجاري',
  `contact_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? رقم الهاتف التجاري',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'هل المورد موثّق من إدارة المنصة؟',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ اعتماد المورد بعد المراجعة',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'هل الحساب نشط ويمكن للمورد الدخول؟',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'سبب رفض طلب التسجيل (إن وُجد)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ الحذف المنطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_supplier_company_name` (`company_name`),
  KEY `suppliers_created_by_foreign` (`created_by`),
  KEY `suppliers_updated_by_foreign` (`updated_by`),
  KEY `supplier_search_index` (`company_name`,`country`,`city`),
  KEY `supplier_status_index` (`user_id`,`is_verified`),
  KEY `suppliers_is_verified_index` (`is_verified`),
  KEY `suppliers_is_active_index` (`is_active`),
  CONSTRAINT `suppliers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'اسم نوع المستخدم: Admin, Supplier, Buyer, ...',
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'slug برمجي اختياري',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'وصف نوع المستخدم',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'حذف منطقي للسجل دون فقدان البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_types_name_unique` (`name`),
  UNIQUE KEY `user_types_slug_unique` (`slug`),
  KEY `user_types_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '? المعرّف الأساسي للمستخدم',
  `user_type_id` bigint unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? اسم المستخدم الكامل',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? البريد الإلكتروني (أساسي للمصادقة)',
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? رقم الهاتف (اختياري)',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT '⏱️ وقت تحقق البريد الإلكتروني',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? كلمة المرور المشفّرة',
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT '⚙️ حالة الحساب',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '? رمز تذكّر الجلسة (login token)',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '?️ حذف منطقي دون فقد البيانات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_user_type_id_foreign` (`user_type_id`),
  KEY `users_created_by_foreign` (`created_by`),
  KEY `users_updated_by_foreign` (`updated_by`),
  KEY `users_phone_index` (`phone`),
  KEY `users_status_index` (`status`),
  CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2025_10_31_000001_create_user_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2025_10_31_000002_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2025_10_31_000003_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_10_31_000004_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_10_31_000005_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_10_31_000006_create_cache_locks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_10_31_000007_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_10_31_000008_create_job_batches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_10_31_000009_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_10_31_000010_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_10_31_000011_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_10_31_000012_create_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_10_31_000013_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_10_31_000014_create_suppliers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_10_31_000015_create_product_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_10_31_000016_create_buyers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_10_31_000018_create_products_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_10_31_000019_create_product_supplier_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_10_31_000020_create_rfqs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_10_31_000021_create_quotations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_10_31_000022_create_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_10_31_000023_create_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_10_31_000024_create_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_10_31_000025_create_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_11_02_191341_create_rfq_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_11_03_130302_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_11_13_000004_create_quotation_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_11_13_000005_create_order_items_table',1);
