CREATE TABLE "actions" (
"id" REAL(11) NOT NULL,
"title" TEXT(255) NOT NULL DEFAULT ' ',
"protected" REAL(6) NOT NULL DEFAULT '0',
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "idx_actions_table_title" ON "actions" ("title" );

CREATE TABLE "application_extension_instances" (
"application_id" REAL(11) NOT NULL,
"extension_instance_id" REAL(11) NOT NULL,
PRIMARY KEY ("application_id", "extension_instance_id") ,
CONSTRAINT "fk_application_extension_instances_applications_1" FOREIGN KEY ("application_id") REFERENCES "applications" ("id"),
CONSTRAINT "fk_application_extension_instances_extension_instances_1" FOREIGN KEY ("extension_instance_id") REFERENCES "extension_instances" ("id")
);

CREATE INDEX "fk_application_extensions_applications_index" ON "application_extension_instances" ("application_id" );
CREATE INDEX "fk_application_extension_instances_extension_instances_index" ON "application_extension_instances" ("extension_instance_id" );

CREATE TABLE "applications" (
"id" REAL(11) NOT NULL,
"catalog_type_id" REAL(11) NOT NULL DEFAULT '2000',
"name" TEXT(255) NOT NULL DEFAULT ' ',
"path" TEXT(2048) NOT NULL DEFAULT ' ',
"description" TEXT DEFAULT NULL,
"customfields" TEXT DEFAULT NULL,
"parameters" TEXT DEFAULT NULL,
"metadata" TEXT DEFAULT NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "fk_applications_catalog_types_index" ON "applications" ("catalog_type_id" );

CREATE TABLE "catalog" (
"id" REAL(11) NOT NULL,
"application_id" REAL(11) NOT NULL DEFAULT '0',
"catalog_type_id" REAL(11) NOT NULL DEFAULT '0',
"source_id" REAL(11) NOT NULL DEFAULT '0',
"enabled" REAL(6) NOT NULL DEFAULT '0',
"redirect_to_id" REAL(11) NOT NULL DEFAULT '0',
"sef_request" TEXT(2048) NOT NULL DEFAULT ' ',
"page_type" TEXT(255) NOT NULL,
"extension_instance_id" REAL(11) NOT NULL DEFAULT '0',
"view_group_id" REAL(11) NOT NULL DEFAULT '0',
"primary_category_id" REAL(11) NOT NULL DEFAULT '0',
PRIMARY KEY ("id") ,
CONSTRAINT "fk_catalog_catalog_types" FOREIGN KEY ("catalog_type_id") REFERENCES "catalog_types" ("id"),
CONSTRAINT "fk_catalog_view_group_id" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id"),
CONSTRAINT "fk_catalog_application_id" FOREIGN KEY ("application_id") REFERENCES "applications" ("id")
);

CREATE UNIQUE INDEX "index_catalog_catalog_types" ON "catalog" ("application_id" , "catalog_type_id" , "source_id" , "enabled" , "redirect_to_id" , "page_type" );
CREATE INDEX "sef_request" ON "catalog" ("application_id" , "enabled" , "redirect_to_id" );
CREATE INDEX "index_catalog_application_id" ON "catalog" ("application_id" );
CREATE INDEX "index_catalog_catalog_type_id" ON "catalog" ("catalog_type_id" );
CREATE INDEX "index_catalog_view_group_id" ON "catalog" ("view_group_id" );
CREATE INDEX "index_catalog_primary_category_id" ON "catalog" ("primary_category_id" );
CREATE INDEX "index_catalog_extension_instance_id" ON "catalog" ("extension_instance_id" );

CREATE TABLE "catalog_activity" (
"id" REAL(11) NOT NULL,
"catalog_id" REAL(11) NOT NULL DEFAULT '0',
"user_id" REAL(11) NOT NULL DEFAULT '0',
"action_id" REAL(11) NOT NULL,
"rating" REAL(6) DEFAULT NULL,
"activity_datetime" TEXT DEFAULT NULL,
"ip_address" TEXT(15) NOT NULL DEFAULT '',
"customfields" TEXT DEFAULT NULL,
PRIMARY KEY ("id") ,
CONSTRAINT "fk_catalog_activity_catalog" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id")
);

CREATE INDEX "catalog_activity_catalog_index" ON "catalog_activity" ("catalog_id" );

CREATE TABLE "catalog_categories" (
"catalog_id" REAL(11) NOT NULL DEFAULT '0',
"category_id" REAL(11) NOT NULL DEFAULT '0',
PRIMARY KEY ("catalog_id", "category_id") ,
CONSTRAINT "fk_catalog_categories_catalog" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id"),
CONSTRAINT "fk_catalog_categories_categories" FOREIGN KEY ("category_id") REFERENCES "content" ("id")
);

CREATE INDEX "fk_catalog_categories_catalog_index" ON "catalog_categories" ("catalog_id" );
CREATE INDEX "fk_catalog_categories_categories_index" ON "catalog_categories" ("category_id" );

CREATE TABLE "catalog_types" (
"id" REAL(11) NOT NULL,
"primary_category_id" REAL(11) NOT NULL,
"title" TEXT(255) NOT NULL,
"alias" TEXT(255) NOT NULL,
"model_type" TEXT(255) NOT NULL,
"model_name" TEXT(255) NOT NULL,
"protected" REAL(6) NOT NULL DEFAULT '0',
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "title" ON "catalog_types" ("title" );
CREATE UNIQUE INDEX "alias" ON "catalog_types" ("alias" );
CREATE UNIQUE INDEX "model_name" ON "catalog_types" ("model_name" );

CREATE TABLE "content" (
"id" REAL(11) NOT NULL,
"site_id" REAL(11) NOT NULL DEFAULT '0',
"extension_instance_id" REAL(11) NOT NULL DEFAULT '0',
"catalog_type_id" REAL(11) NOT NULL DEFAULT '0',
"title" TEXT(255) NOT NULL DEFAULT ' ',
"subtitle" TEXT(255) NOT NULL DEFAULT ' ',
"path" TEXT(2048) NOT NULL DEFAULT ' ',
"alias" TEXT(255) NOT NULL DEFAULT ' ',
"content_text" TEXT DEFAULT NULL,
"protected" REAL(6) NOT NULL DEFAULT '0',
"featured" REAL(6) NOT NULL DEFAULT '0',
"stickied" REAL(6) NOT NULL DEFAULT '0',
"status" REAL(6) NOT NULL DEFAULT '0',
"start_publishing_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"stop_publishing_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"version" REAL(11) NOT NULL DEFAULT '1',
"version_of_id" REAL(11) NOT NULL DEFAULT '0',
"status_prior_to_version" REAL(11) NOT NULL DEFAULT '0',
"created_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"created_by" REAL(11) NOT NULL DEFAULT '0',
"modified_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"modified_by" REAL(11) NOT NULL DEFAULT '0',
"checked_out_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"checked_out_by" REAL(11) NOT NULL DEFAULT '0',
"root" REAL(11) NOT NULL DEFAULT '0',
"parent_id" REAL(11) NOT NULL DEFAULT '0',
"lft" REAL(11) NOT NULL DEFAULT '0',
"rgt" REAL(11) NOT NULL DEFAULT '0',
"lvl" REAL(11) NOT NULL DEFAULT '0',
"home" REAL(6) NOT NULL DEFAULT '0',
"customfields" TEXT DEFAULT NULL,
"parameters" TEXT DEFAULT NULL,
"metadata" TEXT DEFAULT NULL,
"language" TEXT(7) NOT NULL DEFAULT 'en-GB',
"translation_of_id" REAL(11) NOT NULL DEFAULT '0',
"ordering" REAL(11) NOT NULL DEFAULT '0',
PRIMARY KEY ("id") ,
CONSTRAINT "fk_content_extension_instances" FOREIGN KEY ("extension_instance_id") REFERENCES "extension_instances" ("id")
);

CREATE UNIQUE INDEX "alias" ON "content" ("catalog_type_id" , "alias" );
CREATE INDEX "fk_content_extension_instance_id" ON "content" ("extension_instance_id" );
CREATE INDEX "fk_content_catalog_type_id" ON "content" ("catalog_type_id" );
CREATE INDEX "fk_content_site_id" ON "content" ("site_id" );

CREATE TABLE "extension_instances" (
"id" REAL(11) NOT NULL,
"extension_id" REAL(11) NOT NULL,
"catalog_type_id" REAL(11) NOT NULL,
"title" TEXT(255) NOT NULL DEFAULT ' ',
"subtitle" TEXT(255) NOT NULL DEFAULT ' ',
"path" TEXT(2048) NOT NULL DEFAULT ' ',
"alias" TEXT(255) NOT NULL DEFAULT ' ',
"menu" TEXT(255) NOT NULL,
"page_type" TEXT(255) NOT NULL,
"content_text" TEXT DEFAULT NULL,
"protected" REAL(6) NOT NULL DEFAULT '0',
"featured" REAL(6) NOT NULL DEFAULT '0',
"stickied" REAL(6) NOT NULL DEFAULT '0',
"status" REAL(6) NOT NULL DEFAULT '0',
"start_publishing_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"stop_publishing_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"version" REAL(11) NOT NULL DEFAULT '1',
"version_of_id" REAL(11) NOT NULL DEFAULT '0',
"status_prior_to_version" REAL(11) NOT NULL DEFAULT '0',
"created_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"created_by" REAL(11) NOT NULL DEFAULT '0',
"modified_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"modified_by" REAL(11) NOT NULL DEFAULT '0',
"checked_out_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"checked_out_by" REAL(11) NOT NULL DEFAULT '0',
"root" REAL(11) NOT NULL DEFAULT '0',
"parent_id" REAL(11) NOT NULL DEFAULT '0',
"lft" REAL(11) NOT NULL DEFAULT '0',
"rgt" REAL(11) NOT NULL DEFAULT '0',
"lvl" REAL(11) NOT NULL DEFAULT '0',
"home" REAL(6) NOT NULL DEFAULT '0',
"customfields" TEXT DEFAULT NULL,
"parameters" TEXT DEFAULT NULL,
"metadata" TEXT DEFAULT NULL,
"language" TEXT(7) NOT NULL DEFAULT 'en-GB',
"translation_of_id" REAL(11) NOT NULL DEFAULT '0',
"ordering" REAL(11) NOT NULL DEFAULT '0',
PRIMARY KEY ("id") ,
CONSTRAINT "fk_extension_instances_extensions" FOREIGN KEY ("extension_id") REFERENCES "extensions" ("id")
);

CREATE INDEX "fk_extension_instances_extensions_index" ON "extension_instances" ("extension_id" );
CREATE INDEX "fk_extension_instances_catalog_type_index" ON "extension_instances" ("catalog_type_id" );

CREATE TABLE "extension_sites" (
"id" REAL(11) NOT NULL,
"name" TEXT(255) DEFAULT ' ',
"enabled" REAL(6) NOT NULL DEFAULT '0',
"location" TEXT(2048) NOT NULL,
"customfields" TEXT DEFAULT NULL,
"parameters" TEXT DEFAULT NULL,
"metadata" TEXT DEFAULT NULL,
PRIMARY KEY ("id")
);

CREATE TABLE "extensions" (
"id" REAL(11) NOT NULL,
"extension_site_id" REAL(11) NOT NULL DEFAULT '0',
"catalog_type_id" REAL(11) NOT NULL,
"name" TEXT(255) NOT NULL DEFAULT ' ',
"subtitle" TEXT(255) NOT NULL DEFAULT ' ',
"language" TEXT(7) NOT NULL DEFAULT 'en-GB',
"translation_of_id" REAL(11) NOT NULL DEFAULT '0',
"ordering" REAL(11) NOT NULL DEFAULT '0',
PRIMARY KEY ("id") ,
CONSTRAINT "fk_extensions_extension_sites_1" FOREIGN KEY ("extension_site_id") REFERENCES "extension_sites" ("id")
);

CREATE INDEX "extensions_extension_sites_index" ON "extensions" ("extension_site_id" );
CREATE INDEX "fk_extension_catalog_type_index" ON "extensions" ("catalog_type_id" );

CREATE TABLE "group_permissions" (
"id" REAL(11) NOT NULL,
"group_id" REAL(11) NOT NULL,
"catalog_id" REAL(11) NOT NULL,
"action_id" REAL(11) NOT NULL,
PRIMARY KEY ("id") ,
CONSTRAINT "fk_group_permissions_actions_1" FOREIGN KEY ("action_id") REFERENCES "actions" ("id"),
CONSTRAINT "fk_group_permissions_content_1" FOREIGN KEY ("group_id") REFERENCES "content" ("id"),
CONSTRAINT "fk_group_permissions_catalog_1" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id")
);

CREATE INDEX "fk_group_permissions_actions_index" ON "group_permissions" ("action_id" );
CREATE INDEX "fk_group_permissions_content_index" ON "group_permissions" ("group_id" );
CREATE INDEX "fk_group_permissions_catalog_index" ON "group_permissions" ("catalog_id" );

CREATE TABLE "group_view_groups" (
"group_id" REAL(11) NOT NULL,
"view_group_id" REAL(11) NOT NULL,
PRIMARY KEY ("view_group_id", "group_id") ,
CONSTRAINT "fk_group_view_groups_groups" FOREIGN KEY ("group_id") REFERENCES "content" ("id"),
CONSTRAINT "fk_group_view_groups_view_groups" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id")
);

CREATE INDEX "fk_group_view_groups_view_groups_index" ON "group_view_groups" ("view_group_id" );
CREATE INDEX "fk_group_view_groups_groups_index" ON "group_view_groups" ("group_id" );

CREATE TABLE "log" (
"id" REAL(11) NOT NULL,
"priority" REAL(11) DEFAULT NULL,
"message" TEXT DEFAULT NULL,
"date" TEXT DEFAULT NULL,
"category" TEXT(255) DEFAULT NULL,
"customfields" TEXT,
PRIMARY KEY ("id")
);

CREATE INDEX "idx_category_date_priority" ON "log" ("category" , "date" , "priority" );

CREATE TABLE "sessions" (
"session_id" TEXT(255) NOT NULL,
"application_id" REAL(11) NOT NULL,
"session_time" TEXT,
"data" TEXT DEFAULT NULL,
"user_id" REAL(11) DEFAULT '0',
PRIMARY KEY ("session_id")
);

CREATE INDEX "fk_sessions_applications_index" ON "sessions" ("application_id" );

CREATE TABLE "site_applications" (
"application_id" REAL(11) NOT NULL,
"site_id" REAL(11) NOT NULL,
PRIMARY KEY ("site_id", "application_id") ,
CONSTRAINT "fk_site_applications_applications" FOREIGN KEY ("application_id") REFERENCES "applications" ("id"),
CONSTRAINT "fk_site_applications_sites" FOREIGN KEY ("site_id") REFERENCES "sites" ("id")
);

CREATE INDEX "fk_site_applications_sites_index" ON "site_applications" ("site_id" );
CREATE INDEX "fk_site_applications_applications_index" ON "site_applications" ("application_id" );

CREATE TABLE "site_extension_instances" (
"site_id" REAL(11) NOT NULL,
"extension_instance_id" REAL(11) NOT NULL,
PRIMARY KEY ("site_id", "extension_instance_id") ,
CONSTRAINT "fk_site_extension_instances_sites_1" FOREIGN KEY ("site_id") REFERENCES "sites" ("id"),
CONSTRAINT "fk_site_extension_instances_extension_instances_1" FOREIGN KEY ("extension_instance_id") REFERENCES "extension_instances" ("id")
);

CREATE INDEX "fk_application_extensions_sites_index" ON "site_extension_instances" ("site_id" );
CREATE INDEX "fk_application_extension_instances_extension_instances_index" ON "site_extension_instances" ("extension_instance_id" );

CREATE TABLE "sites" (
"id" REAL(11) NOT NULL,
"catalog_type_id" REAL(11) NOT NULL DEFAULT '1000',
"name" TEXT(255) NOT NULL DEFAULT ' ',
"path" TEXT(2048) NOT NULL DEFAULT ' ',
"base_url" TEXT(2048) NOT NULL DEFAULT 'Used only as documentation',
"description" TEXT DEFAULT NULL,
"customfields" TEXT DEFAULT NULL,
"parameters" TEXT DEFAULT NULL,
"metadata" TEXT DEFAULT NULL,
PRIMARY KEY ("id")
);

CREATE TABLE "user_activity" (
"id" REAL(11) NOT NULL,
"user_id" REAL(11) NOT NULL DEFAULT '0',
"action_id" REAL(11) NOT NULL DEFAULT '0',
"catalog_id" REAL(11) NOT NULL DEFAULT '0',
"activity_datetime" TEXT DEFAULT NULL,
"ip_address" TEXT(15) NOT NULL DEFAULT '',
PRIMARY KEY ("id") ,
CONSTRAINT "fk_user_activity_users_1" FOREIGN KEY ("user_id") REFERENCES "users" ("id"),
CONSTRAINT "fk_user_activity_catalog_1" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id")
);

CREATE INDEX "user_activity_user_index" ON "user_activity" ("user_id" );
CREATE INDEX "user_activity_catalog_index" ON "user_activity" ("catalog_id" );
CREATE INDEX "user_activity_action_index" ON "user_activity" ("action_id" );

CREATE TABLE "user_applications" (
"user_id" REAL(11) NOT NULL,
"application_id" REAL(11) NOT NULL,
PRIMARY KEY ("application_id", "user_id") ,
CONSTRAINT "fk_user_applications_applications" FOREIGN KEY ("application_id") REFERENCES "applications" ("id"),
CONSTRAINT "fk_user_applications_users" FOREIGN KEY ("user_id") REFERENCES "users" ("id")
);

CREATE INDEX "fk_user_applications_users_index" ON "user_applications" ("user_id" );
CREATE INDEX "fk_user_applications_applications_index" ON "user_applications" ("application_id" );

CREATE TABLE "user_groups" (
"user_id" REAL(11) NOT NULL,
"group_id" REAL(11) NOT NULL,
PRIMARY KEY ("group_id", "user_id") ,
CONSTRAINT "fk_user_groups_users_1" FOREIGN KEY ("user_id") REFERENCES "users" ("id"),
CONSTRAINT "fk_user_groups_content_1" FOREIGN KEY ("group_id") REFERENCES "content" ("id")
);

CREATE INDEX "fk_molajo_user_groups_molajo_users_index" ON "user_groups" ("user_id" );
CREATE INDEX "fk_molajo_user_groups_molajo_groups_index" ON "user_groups" ("group_id" );

CREATE TABLE "user_view_groups" (
"user_id" REAL(11) NOT NULL,
"view_group_id" REAL(11) NOT NULL,
PRIMARY KEY ("view_group_id", "user_id") ,
CONSTRAINT "fk_user_view_groups_users_1" FOREIGN KEY ("user_id") REFERENCES "users" ("id"),
CONSTRAINT "fk_user_view_groups_view_groups_1" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id")
);

CREATE INDEX "fk_user_groups_users_index" ON "user_view_groups" ("user_id" );
CREATE INDEX "fk_user_view_groups_view_groups_index" ON "user_view_groups" ("view_group_id" );

CREATE TABLE "users" (
"id" REAL(11) NOT NULL,
"site_id" REAL(11) NOT NULL DEFAULT '0',
"catalog_type_id" REAL(11) NOT NULL DEFAULT '0',
"username" TEXT(255) NOT NULL,
"first_name" TEXT(100) DEFAULT '',
"last_name" TEXT(150) DEFAULT '',
"full_name" TEXT(255) NOT NULL,
"alias" TEXT(255) NOT NULL,
"content_text" TEXT DEFAULT NULL,
"email" TEXT(255) DEFAULT '  ',
"password" TEXT(100) NOT NULL DEFAULT '  ',
"block" REAL(6) NOT NULL DEFAULT '0',
"register_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"activation_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"last_visit_datetime" TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
"customfields" TEXT DEFAULT NULL,
"parameters" TEXT DEFAULT NULL,
"metadata" TEXT DEFAULT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "username" ON "users" ("username" );
CREATE UNIQUE INDEX "email" ON "users" ("email" );
CREATE INDEX "last_name_first_name" ON "users" ("last_name" , "first_name" );
CREATE INDEX "fk_users_sites_index" ON "users" ("site_id" );

CREATE TABLE "view_group_permissions" (
"id" REAL(11) NOT NULL,
"view_group_id" REAL(11) NOT NULL,
"catalog_id" REAL(11) NOT NULL,
"action_id" REAL(11) NOT NULL,
PRIMARY KEY ("id") ,
CONSTRAINT "fk_view_group_permissions_actions_1" FOREIGN KEY ("action_id") REFERENCES "actions" ("id"),
CONSTRAINT "fk_view_group_permissions_view_groups_1" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id"),
CONSTRAINT "fk_view_group_permissions_catalog_1" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id")
);

CREATE INDEX "fk_view_group_permissions_view_groups_index" ON "view_group_permissions" ("view_group_id" );
CREATE INDEX "fk_view_group_permissions_actions_index" ON "view_group_permissions" ("action_id" );
CREATE INDEX "fk_view_group_permissions_catalog_index" ON "view_group_permissions" ("catalog_id" );

CREATE TABLE "view_groups" (
"id" REAL(11) NOT NULL,
"view_group_name_list" TEXT NOT NULL,
"view_group_id_list" TEXT NOT NULL,
PRIMARY KEY ("id")
);

