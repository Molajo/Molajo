CREATE TABLE "actions" (
"id" NUMBER(11) NOT NULL,
"title" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"protected" NUMBER(6) DEFAULT '0' NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "idx_actions_table_title" ON "actions" ("title" );
COMMENT ON COLUMN "actions"."id" IS 'Actions Primary Key';

CREATE TABLE "application_extension_instances" (
"application_id" NUMBER(11) NOT NULL,
"extension_instance_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("application_id", "extension_instance_id")
);

CREATE INDEX "fk_application_extensions_applications_index" ON "application_extension_instances" ("application_id" );
CREATE INDEX "fk_application_extension_instances_extension_instances_index" ON "application_extension_instances" ("extension_instance_id" );

CREATE TABLE "applications" (
"id" NUMBER(11) NOT NULL,
"catalog_type_id" NUMBER(11) DEFAULT '2000' NOT NULL,
"name" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"path" VARCHAR2(2048) DEFAULT ' ' NOT NULL,
"description" CLOB DEFAULT NULL NULL,
"customfields" CLOB DEFAULT NULL NULL,
"parameters" CLOB DEFAULT NULL NULL,
"metadata" CLOB DEFAULT NULL NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "fk_applications_catalog_types_index" ON "applications" ("catalog_type_id" );
COMMENT ON COLUMN "applications"."id" IS 'Application Primary Key';
COMMENT ON COLUMN "applications"."catalog_type_id" IS 'Catalog Type ID';
COMMENT ON COLUMN "applications"."name" IS 'Application Name';
COMMENT ON COLUMN "applications"."path" IS 'Application Path';
COMMENT ON COLUMN "applications"."description" IS 'Application Description';
COMMENT ON COLUMN "applications"."customfields" IS 'Custom Fields for this Application';
COMMENT ON COLUMN "applications"."parameters" IS 'Custom Parameters for this Application';
COMMENT ON COLUMN "applications"."metadata" IS 'Metadata definitions for this Application';

CREATE TABLE "catalog" (
"id" NUMBER(11) NOT NULL,
"application_id" NUMBER(11) DEFAULT '0' NOT NULL,
"catalog_type_id" NUMBER(11) DEFAULT '0' NOT NULL,
"source_id" NUMBER(11) DEFAULT '0' NOT NULL,
"enabled" NUMBER(6) DEFAULT '0' NOT NULL,
"redirect_to_id" NUMBER(11) DEFAULT '0' NOT NULL,
"sef_request" VARCHAR2(2048) DEFAULT ' ' NOT NULL,
"page_type" VARCHAR2(255) NOT NULL,
"extension_instance_id" NUMBER(11) DEFAULT '0' NOT NULL,
"view_group_id" NUMBER(11) DEFAULT '0' NOT NULL,
"primary_category_id" NUMBER(11) DEFAULT '0' NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "index_catalog_catalog_types" ON "catalog" ("application_id" , "catalog_type_id" , "source_id" , "enabled" , "redirect_to_id" , "page_type" );
CREATE INDEX "sef_request" ON "catalog" ("application_id" , "enabled" , "redirect_to_id" );
CREATE INDEX "index_catalog_application_id" ON "catalog" ("application_id" );
CREATE INDEX "index_catalog_catalog_type_id" ON "catalog" ("catalog_type_id" );
CREATE INDEX "index_catalog_view_group_id" ON "catalog" ("view_group_id" );
CREATE INDEX "index_catalog_primary_category_id" ON "catalog" ("primary_category_id" );
CREATE INDEX "index_catalog_extension_instance_id" ON "catalog" ("extension_instance_id" );
COMMENT ON COLUMN "catalog"."id" IS 'Catalog Primary Key';
COMMENT ON COLUMN "catalog"."application_id" IS 'Application ID';
COMMENT ON COLUMN "catalog"."catalog_type_id" IS 'Catalog Type ID';
COMMENT ON COLUMN "catalog"."source_id" IS 'Primary Key of source data stored in table associated with Catalog Type ID Model';
COMMENT ON COLUMN "catalog"."enabled" IS 'Enabled - 1 or Disabled - 0';
COMMENT ON COLUMN "catalog"."redirect_to_id" IS 'Redirect to Catalog ID';
COMMENT ON COLUMN "catalog"."sef_request" IS 'SEF Request';
COMMENT ON COLUMN "catalog"."page_type" IS 'Menu Item Type includes such values as Item, List, or a specific Menuitem Type';
COMMENT ON COLUMN "catalog"."extension_instance_id" IS 'Extension Instance ID';
COMMENT ON COLUMN "catalog"."view_group_id" IS 'View Group ID';
COMMENT ON COLUMN "catalog"."primary_category_id" IS 'Primary Category ID';

CREATE TABLE "catalog_activity" (
"id" NUMBER(11) NOT NULL,
"catalog_id" NUMBER(11) DEFAULT '0' NOT NULL,
"user_id" NUMBER(11) DEFAULT '0' NOT NULL,
"action_id" NUMBER(11) NOT NULL,
"rating" NUMBER(6) DEFAULT NULL NULL,
"activity_datetime" DATE DEFAULT NULL NULL,
"ip_address" CHAR(15) DEFAULT '' NOT NULL,
"customfields" CLOB DEFAULT NULL NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "catalog_activity_catalog_index" ON "catalog_activity" ("catalog_id" );

CREATE TABLE "catalog_categories" (
"catalog_id" NUMBER(11) DEFAULT '0' NOT NULL,
"category_id" NUMBER(11) DEFAULT '0' NOT NULL,
PRIMARY KEY ("catalog_id", "category_id")
);

CREATE INDEX "fk_catalog_categories_catalog_index" ON "catalog_categories" ("catalog_id" );
CREATE INDEX "fk_catalog_categories_categories_index" ON "catalog_categories" ("category_id" );

CREATE TABLE "catalog_types" (
"id" NUMBER(11) NOT NULL,
"primary_category_id" NUMBER(11) NOT NULL,
"title" VARCHAR2(255) NOT NULL,
"alias" VARCHAR2(255) NOT NULL,
"model_type" VARCHAR2(255) NOT NULL,
"model_name" VARCHAR2(255) NOT NULL,
"protected" NUMBER(6) DEFAULT '0' NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "title" ON "catalog_types" ("title" );
CREATE UNIQUE INDEX "alias" ON "catalog_types" ("alias" );
CREATE UNIQUE INDEX "model_name" ON "catalog_types" ("model_name" );
COMMENT ON COLUMN "catalog_types"."id" IS 'Catalog Types Primary Key';
COMMENT ON COLUMN "catalog_types"."primary_category_id" IS 'Primary Category ID';
COMMENT ON COLUMN "catalog_types"."title" IS 'Catalog Type Title';
COMMENT ON COLUMN "catalog_types"."alias" IS 'Catalog Type Alias';
COMMENT ON COLUMN "catalog_types"."model_type" IS 'Catalog Type Model Type';
COMMENT ON COLUMN "catalog_types"."model_name" IS 'Catalog Type Model Name';
COMMENT ON COLUMN "catalog_types"."protected" IS 'Protected from system removal';

CREATE TABLE "content" (
"id" NUMBER(11) NOT NULL,
"site_id" NUMBER(11) DEFAULT '0' NOT NULL,
"extension_instance_id" NUMBER(11) DEFAULT '0' NOT NULL,
"catalog_type_id" NUMBER(11) DEFAULT '0' NOT NULL,
"title" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"subtitle" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"path" VARCHAR2(2048) DEFAULT ' ' NOT NULL,
"alias" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"content_text" CLOB DEFAULT NULL NULL,
"protected" NUMBER(6) DEFAULT '0' NOT NULL,
"featured" NUMBER(6) DEFAULT '0' NOT NULL,
"stickied" NUMBER(6) DEFAULT '0' NOT NULL,
"status" NUMBER(6) DEFAULT '0' NOT NULL,
"start_publishing_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"stop_publishing_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"version" NUMBER(11) DEFAULT '1' NOT NULL,
"version_of_id" NUMBER(11) DEFAULT '0' NOT NULL,
"status_prior_to_version" NUMBER(11) DEFAULT '0' NOT NULL,
"created_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"created_by" NUMBER(11) DEFAULT '0' NOT NULL,
"modified_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"modified_by" NUMBER(11) DEFAULT '0' NOT NULL,
"checked_out_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"checked_out_by" NUMBER(11) DEFAULT '0' NOT NULL,
"root" NUMBER(11) DEFAULT '0' NOT NULL,
"parent_id" NUMBER(11) DEFAULT '0' NOT NULL,
"lft" NUMBER(11) DEFAULT '0' NOT NULL,
"rgt" NUMBER(11) DEFAULT '0' NOT NULL,
"lvl" NUMBER(11) DEFAULT '0' NOT NULL,
"home" NUMBER(6) DEFAULT '0' NOT NULL,
"customfields" CLOB DEFAULT NULL NULL,
"parameters" CLOB DEFAULT NULL NULL,
"metadata" CLOB DEFAULT NULL NULL,
"language" CHAR(7) DEFAULT 'en-GB' NOT NULL,
"translation_of_id" NUMBER(11) DEFAULT '0' NOT NULL,
"ordering" NUMBER(11) DEFAULT '0' NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "alias" ON "content" ("catalog_type_id" , "alias" );
CREATE INDEX "fk_content_extension_instance_id" ON "content" ("extension_instance_id" );
CREATE INDEX "fk_content_catalog_type_id" ON "content" ("catalog_type_id" );
CREATE INDEX "fk_content_site_id" ON "content" ("site_id" );
COMMENT ON COLUMN "content"."id" IS 'Content Table Primary Key';
COMMENT ON COLUMN "content"."site_id" IS 'Site Primary Key or 0';
COMMENT ON COLUMN "content"."extension_instance_id" IS 'Extension Instance Primary Key';
COMMENT ON COLUMN "content"."catalog_type_id" IS 'Catalog Type Primary Key';
COMMENT ON COLUMN "content"."title" IS 'Title';
COMMENT ON COLUMN "content"."subtitle" IS 'Subtitle';
COMMENT ON COLUMN "content"."path" IS 'URI Path to append to Alias';
COMMENT ON COLUMN "content"."alias" IS 'Slug, or alias, associated with Title, must be unique when combined with path.';
COMMENT ON COLUMN "content"."content_text" IS 'Text field';
COMMENT ON COLUMN "content"."protected" IS 'If activated, represents an important feature required for operations that cannot be removed.';
COMMENT ON COLUMN "content"."featured" IS 'Indicator representing content designated as Featured. Can be used in queries.';
COMMENT ON COLUMN "content"."stickied" IS 'Indicator representing content designated as Stickied. Can be used in queries.';
COMMENT ON COLUMN "content"."status" IS 'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version';
COMMENT ON COLUMN "content"."start_publishing_datetime" IS 'Publish Begin Date and Time';
COMMENT ON COLUMN "content"."stop_publishing_datetime" IS 'Publish End Date and Time';
COMMENT ON COLUMN "content"."version" IS 'Version Number';
COMMENT ON COLUMN "content"."version_of_id" IS 'Primary Key for this Version';
COMMENT ON COLUMN "content"."status_prior_to_version" IS 'State value prior to creating this version, can be used to determine if content was just published';
COMMENT ON COLUMN "content"."created_datetime" IS 'Created Date and Time';
COMMENT ON COLUMN "content"."created_by" IS 'Created by User ID';
COMMENT ON COLUMN "content"."modified_datetime" IS 'Modified Date';
COMMENT ON COLUMN "content"."modified_by" IS 'Modified By User ID';
COMMENT ON COLUMN "content"."checked_out_datetime" IS 'Checked out Date and Time';
COMMENT ON COLUMN "content"."checked_out_by" IS 'Checked out by User Id';
COMMENT ON COLUMN "content"."root" IS 'Used with Hierarchical Data to indicate the root node for the tree';
COMMENT ON COLUMN "content"."parent_id" IS 'Used with Hierarchical Data to indicate the parent for this node.';
COMMENT ON COLUMN "content"."lft" IS 'Number which increases from the root node in sequential order until the lowest branch is reached.';
COMMENT ON COLUMN "content"."rgt" IS 'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.';
COMMENT ON COLUMN "content"."lvl" IS 'Number representing the heirarchical level of the content. The number one is the first level. ';
COMMENT ON COLUMN "content"."customfields" IS 'Custom Fields for this Resource Item';
COMMENT ON COLUMN "content"."parameters" IS 'Custom Parameters for this Resource Item';
COMMENT ON COLUMN "content"."metadata" IS 'Metadata definitions for this Resource Item';
COMMENT ON COLUMN "content"."ordering" IS 'Ordering';

CREATE TABLE "extension_instances" (
"id" NUMBER(11) NOT NULL,
"extension_id" NUMBER(11) NOT NULL,
"catalog_type_id" NUMBER(11) NOT NULL,
"title" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"subtitle" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"path" VARCHAR2(2048) DEFAULT ' ' NOT NULL,
"alias" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"menu" VARCHAR2(255) NOT NULL,
"page_type" VARCHAR2(255) NOT NULL,
"content_text" CLOB DEFAULT NULL NULL,
"protected" NUMBER(6) DEFAULT '0' NOT NULL,
"featured" NUMBER(6) DEFAULT '0' NOT NULL,
"stickied" NUMBER(6) DEFAULT '0' NOT NULL,
"status" NUMBER(6) DEFAULT '0' NOT NULL,
"start_publishing_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"stop_publishing_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"version" NUMBER(11) DEFAULT '1' NOT NULL,
"version_of_id" NUMBER(11) DEFAULT '0' NOT NULL,
"status_prior_to_version" NUMBER(11) DEFAULT '0' NOT NULL,
"created_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"created_by" NUMBER(11) DEFAULT '0' NOT NULL,
"modified_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"modified_by" NUMBER(11) DEFAULT '0' NOT NULL,
"checked_out_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"checked_out_by" NUMBER(11) DEFAULT '0' NOT NULL,
"root" NUMBER(11) DEFAULT '0' NOT NULL,
"parent_id" NUMBER(11) DEFAULT '0' NOT NULL,
"lft" NUMBER(11) DEFAULT '0' NOT NULL,
"rgt" NUMBER(11) DEFAULT '0' NOT NULL,
"lvl" NUMBER(11) DEFAULT '0' NOT NULL,
"home" NUMBER(6) DEFAULT '0' NOT NULL,
"customfields" CLOB DEFAULT NULL NULL,
"parameters" CLOB DEFAULT NULL NULL,
"metadata" CLOB DEFAULT NULL NULL,
"language" CHAR(7) DEFAULT 'en-GB' NOT NULL,
"translation_of_id" NUMBER(11) DEFAULT '0' NOT NULL,
"ordering" NUMBER(11) DEFAULT '0' NOT NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "fk_extension_instances_extensions_index" ON "extension_instances" ("extension_id" );
CREATE INDEX "fk_extension_instances_catalog_type_index" ON "extension_instances" ("catalog_type_id" );
COMMENT ON COLUMN "extension_instances"."id" IS 'Extension Instance Primary Key';
COMMENT ON COLUMN "extension_instances"."extension_id" IS 'Extension Primary Key';
COMMENT ON COLUMN "extension_instances"."catalog_type_id" IS 'Catalog Type ID';
COMMENT ON COLUMN "extension_instances"."title" IS 'Title';
COMMENT ON COLUMN "extension_instances"."subtitle" IS 'Subtitle';
COMMENT ON COLUMN "extension_instances"."path" IS 'Path prepended to alias to create URL';
COMMENT ON COLUMN "extension_instances"."alias" IS 'URI Alias of Title';
COMMENT ON COLUMN "extension_instances"."menu" IS 'For Menuitem content types, contains the name of the associated Menu';
COMMENT ON COLUMN "extension_instances"."page_type" IS 'For Menuitem content types, contains the name of the associated Menuitem Type';
COMMENT ON COLUMN "extension_instances"."content_text" IS 'Information about the Extension';
COMMENT ON COLUMN "extension_instances"."protected" IS 'If activated, represents an important feature required for operations that cannot be removed.';
COMMENT ON COLUMN "extension_instances"."featured" IS 'Indicator representing content designated as Featured. Can be used in queries.';
COMMENT ON COLUMN "extension_instances"."stickied" IS 'Indicator representing content designated as Stickied. Can be used in queries.';
COMMENT ON COLUMN "extension_instances"."status" IS 'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version';
COMMENT ON COLUMN "extension_instances"."start_publishing_datetime" IS 'Publish Begin Date and Time';
COMMENT ON COLUMN "extension_instances"."stop_publishing_datetime" IS 'Publish End Date and Time';
COMMENT ON COLUMN "extension_instances"."version" IS 'Version Number';
COMMENT ON COLUMN "extension_instances"."version_of_id" IS 'Primary Key for this Version';
COMMENT ON COLUMN "extension_instances"."status_prior_to_version" IS 'State value prior to creating this version, can be used to determine if content was just published';
COMMENT ON COLUMN "extension_instances"."created_datetime" IS 'Created Date and Time';
COMMENT ON COLUMN "extension_instances"."created_by" IS 'Created by User ID';
COMMENT ON COLUMN "extension_instances"."modified_datetime" IS 'Modified Date';
COMMENT ON COLUMN "extension_instances"."modified_by" IS 'Modified By User ID';
COMMENT ON COLUMN "extension_instances"."checked_out_datetime" IS 'Checked out Date and Time';
COMMENT ON COLUMN "extension_instances"."checked_out_by" IS 'Checked out by User Id';
COMMENT ON COLUMN "extension_instances"."root" IS 'Used with Hierarchical Data to indicate the root node for the tree';
COMMENT ON COLUMN "extension_instances"."parent_id" IS 'Used with Hierarchical Data to indicate the parent for this node.';
COMMENT ON COLUMN "extension_instances"."lft" IS 'Number which increases from the root node in sequential order until the lowest branch is reached.';
COMMENT ON COLUMN "extension_instances"."rgt" IS 'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.';
COMMENT ON COLUMN "extension_instances"."lvl" IS 'Number representing the heirarchical level of the content. The number one is the first level. ';
COMMENT ON COLUMN "extension_instances"."customfields" IS 'Custom Fields for this Resource Item';
COMMENT ON COLUMN "extension_instances"."parameters" IS 'Custom Parameters for this Resource Item';
COMMENT ON COLUMN "extension_instances"."metadata" IS 'Metadata definitions for this Resource Item';
COMMENT ON COLUMN "extension_instances"."ordering" IS 'Ordering';

CREATE TABLE "extension_sites" (
"id" NUMBER(11) NOT NULL,
"name" VARCHAR2(255) DEFAULT ' ' NULL,
"enabled" NUMBER(6) DEFAULT '0' NOT NULL,
"location" VARCHAR2(2048) NOT NULL,
"customfields" CLOB DEFAULT NULL NULL,
"parameters" CLOB DEFAULT NULL NULL,
"metadata" CLOB DEFAULT NULL NULL,
PRIMARY KEY ("id")
);

CREATE TABLE "extensions" (
"id" NUMBER(11) NOT NULL,
"extension_site_id" NUMBER(11) DEFAULT '0' NOT NULL,
"catalog_type_id" NUMBER(11) NOT NULL,
"name" CHAR(255) DEFAULT ' ' NOT NULL,
"subtitle" CHAR(255) DEFAULT ' ' NOT NULL,
"language" CHAR(7) DEFAULT 'en-GB' NOT NULL,
"translation_of_id" NUMBER(11) DEFAULT '0' NOT NULL,
"ordering" NUMBER(11) DEFAULT '0' NOT NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "extensions_extension_sites_index" ON "extensions" ("extension_site_id" );
CREATE INDEX "fk_extension_catalog_type_index" ON "extensions" ("catalog_type_id" );
COMMENT ON COLUMN "extensions"."id" IS 'Extension Primary Key';
COMMENT ON COLUMN "extensions"."extension_site_id" IS 'Extension Site ID';
COMMENT ON COLUMN "extensions"."catalog_type_id" IS 'Catalog Type ID';
COMMENT ON COLUMN "extensions"."name" IS 'Name of Extension';
COMMENT ON COLUMN "extensions"."subtitle" IS 'Extension Subtitle (Yes, I know it has no title.)';
COMMENT ON COLUMN "extensions"."translation_of_id" IS 'This data is a translation for this the data with this primary key';
COMMENT ON COLUMN "extensions"."ordering" IS 'Ordering';

CREATE TABLE "group_permissions" (
"id" NUMBER(11) NOT NULL,
"group_id" NUMBER(11) NOT NULL,
"catalog_id" NUMBER(11) NOT NULL,
"action_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "fk_group_permissions_actions_index" ON "group_permissions" ("action_id" );
CREATE INDEX "fk_group_permissions_content_index" ON "group_permissions" ("group_id" );
CREATE INDEX "fk_group_permissions_catalog_index" ON "group_permissions" ("catalog_id" );
COMMENT ON COLUMN "group_permissions"."group_id" IS 'Foreign Key to #_groups.id';
COMMENT ON COLUMN "group_permissions"."catalog_id" IS 'Foreign Key to molajo_catalog.id';
COMMENT ON COLUMN "group_permissions"."action_id" IS 'Foreign Key to molajo_actions.id';

CREATE TABLE "group_view_groups" (
"group_id" NUMBER(11) NOT NULL,
"view_group_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("view_group_id", "group_id")
);

CREATE INDEX "fk_group_view_groups_view_groups_index" ON "group_view_groups" ("view_group_id" );
CREATE INDEX "fk_group_view_groups_groups_index" ON "group_view_groups" ("group_id" );
COMMENT ON COLUMN "group_view_groups"."group_id" IS 'FK to the molajo_group table.';
COMMENT ON COLUMN "group_view_groups"."view_group_id" IS 'FK to the molajo_groupings table.';

CREATE TABLE "log" (
"id" NUMBER(11) NOT NULL,
"priority" NUMBER(11) DEFAULT NULL NULL,
"message" CLOB DEFAULT NULL NULL,
"date" DATE DEFAULT NULL NULL,
"category" VARCHAR2(255) DEFAULT NULL NULL,
"customfields" CLOB NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "idx_category_date_priority" ON "log" ("category" , "date" , "priority" );
COMMENT ON COLUMN "log"."id" IS 'Log Primary Key';

CREATE TABLE "sessions" (
"session_id" VARCHAR2(255) NOT NULL,
"application_id" NUMBER(11) NOT NULL,
"session_time" DATE NULL,
"data" CLOB DEFAULT NULL NULL,
"user_id" NUMBER(11) DEFAULT '0' NULL,
PRIMARY KEY ("session_id")
);

CREATE INDEX "fk_sessions_applications_index" ON "sessions" ("application_id" );

CREATE TABLE "site_applications" (
"application_id" NUMBER(11) NOT NULL,
"site_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("site_id", "application_id")
);

CREATE INDEX "fk_site_applications_sites_index" ON "site_applications" ("site_id" );
CREATE INDEX "fk_site_applications_applications_index" ON "site_applications" ("application_id" );

CREATE TABLE "site_extension_instances" (
"site_id" NUMBER(11) NOT NULL,
"extension_instance_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("site_id", "extension_instance_id")
);

CREATE INDEX "fk_application_extensions_sites_index" ON "site_extension_instances" ("site_id" );
CREATE INDEX "fk_application_extension_instances_extension_instances_index" ON "site_extension_instances" ("extension_instance_id" );

CREATE TABLE "sites" (
"id" NUMBER(11) NOT NULL,
"catalog_type_id" NUMBER(11) DEFAULT '1000' NOT NULL,
"name" VARCHAR2(255) DEFAULT ' ' NOT NULL,
"path" VARCHAR2(2048) DEFAULT ' ' NOT NULL,
"base_url" VARCHAR2(2048) DEFAULT 'Used only as documentation' NOT NULL,
"description" CLOB DEFAULT NULL NULL,
"customfields" CLOB DEFAULT NULL NULL,
"parameters" CLOB DEFAULT NULL NULL,
"metadata" CLOB DEFAULT NULL NULL,
PRIMARY KEY ("id")
);

COMMENT ON COLUMN "sites"."id" IS 'Site Primary Key';
COMMENT ON COLUMN "sites"."catalog_type_id" IS 'Catalog Type ID';
COMMENT ON COLUMN "sites"."name" IS 'Name of Extension';
COMMENT ON COLUMN "sites"."path" IS 'Path for this site within the Sites Folder';
COMMENT ON COLUMN "sites"."description" IS 'Site Description';
COMMENT ON COLUMN "sites"."customfields" IS 'Custom Fields for this Site';
COMMENT ON COLUMN "sites"."parameters" IS 'Custom Parameters for this Site';
COMMENT ON COLUMN "sites"."metadata" IS 'Metadata definitions for this Site';

CREATE TABLE "user_activity" (
"id" NUMBER(11) NOT NULL,
"user_id" NUMBER(11) DEFAULT '0' NOT NULL,
"action_id" NUMBER(11) DEFAULT '0' NOT NULL,
"catalog_id" NUMBER(11) DEFAULT '0' NOT NULL,
"activity_datetime" DATE DEFAULT NULL NULL,
"ip_address" VARCHAR2(15) DEFAULT '' NOT NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "user_activity_user_index" ON "user_activity" ("user_id" );
CREATE INDEX "user_activity_catalog_index" ON "user_activity" ("catalog_id" );
CREATE INDEX "user_activity_action_index" ON "user_activity" ("action_id" );
COMMENT ON COLUMN "user_activity"."id" IS 'User Activity Primary Key';
COMMENT ON COLUMN "user_activity"."user_id" IS 'User ID Foreign Key';
COMMENT ON COLUMN "user_activity"."action_id" IS 'Action ID Foreign Key';
COMMENT ON COLUMN "user_activity"."catalog_id" IS 'Catalog ID Foreign Key';
COMMENT ON COLUMN "user_activity"."activity_datetime" IS 'Activity Datetime';
COMMENT ON COLUMN "user_activity"."ip_address" IS 'IP Address';

CREATE TABLE "user_applications" (
"user_id" NUMBER(11) NOT NULL,
"application_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("application_id", "user_id")
);

CREATE INDEX "fk_user_applications_users_index" ON "user_applications" ("user_id" );
CREATE INDEX "fk_user_applications_applications_index" ON "user_applications" ("application_id" );
COMMENT ON COLUMN "user_applications"."user_id" IS 'User ID Foreign Key';
COMMENT ON COLUMN "user_applications"."application_id" IS 'Application ID Foreign Key';

CREATE TABLE "user_groups" (
"user_id" NUMBER(11) NOT NULL,
"group_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("group_id", "user_id")
);

CREATE INDEX "fk_molajo_user_groups_molajo_users_index" ON "user_groups" ("user_id" );
CREATE INDEX "fk_molajo_user_groups_molajo_groups_index" ON "user_groups" ("group_id" );
COMMENT ON COLUMN "user_groups"."user_id" IS 'Foreign Key to molajo_users.id';
COMMENT ON COLUMN "user_groups"."group_id" IS 'Foreign Key to molajo_groups.id';

CREATE TABLE "user_view_groups" (
"user_id" NUMBER(11) NOT NULL,
"view_group_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("view_group_id", "user_id")
);

CREATE INDEX "fk_user_groups_users_index" ON "user_view_groups" ("user_id" );
CREATE INDEX "fk_user_view_groups_view_groups_index" ON "user_view_groups" ("view_group_id" );
COMMENT ON COLUMN "user_view_groups"."user_id" IS 'Foreign Key to molajo_users.id';
COMMENT ON COLUMN "user_view_groups"."view_group_id" IS 'Foreign Key to molajo_groups.id';

CREATE TABLE "users" (
"id" NUMBER(11) NOT NULL,
"site_id" NUMBER(11) DEFAULT '0' NOT NULL,
"catalog_type_id" NUMBER(11) DEFAULT '0' NOT NULL,
"username" VARCHAR2(255) NOT NULL,
"first_name" VARCHAR2(100) DEFAULT '' NULL,
"last_name" VARCHAR2(150) DEFAULT '' NULL,
"full_name" VARCHAR2(255) NOT NULL,
"alias" VARCHAR2(255) NOT NULL,
"content_text" CLOB DEFAULT NULL NULL,
"email" VARCHAR2(255) DEFAULT '  ' NULL,
"password" VARCHAR2(100) DEFAULT '  ' NOT NULL,
"block" NUMBER(6) DEFAULT '0' NOT NULL,
"register_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"activation_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"last_visit_datetime" DATE DEFAULT '0000-00-00 00:00:00' NOT NULL,
"customfields" CLOB DEFAULT NULL NULL,
"parameters" CLOB DEFAULT NULL NULL,
"metadata" CLOB DEFAULT NULL NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "username" ON "users" ("username" );
CREATE UNIQUE INDEX "email" ON "users" ("email" );
CREATE INDEX "last_name_first_name" ON "users" ("last_name" , "first_name" );
CREATE INDEX "fk_users_sites_index" ON "users" ("site_id" );
COMMENT ON COLUMN "users"."id" IS 'Primary Key for Users';
COMMENT ON COLUMN "users"."site_id" IS 'Site ID Primary Key';
COMMENT ON COLUMN "users"."catalog_type_id" IS 'Catalog Type ID';
COMMENT ON COLUMN "users"."username" IS 'Username';
COMMENT ON COLUMN "users"."first_name" IS 'First name of User';
COMMENT ON COLUMN "users"."last_name" IS 'Last name of User';
COMMENT ON COLUMN "users"."full_name" IS 'Full name of User';
COMMENT ON COLUMN "users"."alias" IS 'User alias';
COMMENT ON COLUMN "users"."content_text" IS 'Text for User';
COMMENT ON COLUMN "users"."email" IS 'Email address of user';
COMMENT ON COLUMN "users"."password" IS 'User password';
COMMENT ON COLUMN "users"."block" IS 'If activiated, blocks user from logging on';
COMMENT ON COLUMN "users"."register_datetime" IS 'Registered date for User';
COMMENT ON COLUMN "users"."activation_datetime" IS 'Activation date for User';
COMMENT ON COLUMN "users"."last_visit_datetime" IS 'Last visit date for User';
COMMENT ON COLUMN "users"."customfields" IS 'Custom Fields for this User';
COMMENT ON COLUMN "users"."parameters" IS 'Custom Parameters for this User';
COMMENT ON COLUMN "users"."metadata" IS 'Metadata definitions for this User';

CREATE TABLE "view_group_permissions" (
"id" NUMBER(11) NOT NULL,
"view_group_id" NUMBER(11) NOT NULL,
"catalog_id" NUMBER(11) NOT NULL,
"action_id" NUMBER(11) NOT NULL,
PRIMARY KEY ("id")
);

CREATE INDEX "fk_view_group_permissions_view_groups_index" ON "view_group_permissions" ("view_group_id" );
CREATE INDEX "fk_view_group_permissions_actions_index" ON "view_group_permissions" ("action_id" );
CREATE INDEX "fk_view_group_permissions_catalog_index" ON "view_group_permissions" ("catalog_id" );
COMMENT ON COLUMN "view_group_permissions"."view_group_id" IS 'Foreign Key to molajo_groups.id';
COMMENT ON COLUMN "view_group_permissions"."catalog_id" IS 'Foreign Key to molajo_catalog.id';
COMMENT ON COLUMN "view_group_permissions"."action_id" IS 'Foreign Key to molajo_actions.id';

CREATE TABLE "view_groups" (
"id" NUMBER(11) NOT NULL,
"view_group_name_list" CLOB NOT NULL,
"view_group_id_list" CLOB NOT NULL,
PRIMARY KEY ("id")
);


ALTER TABLE "catalog" ADD CONSTRAINT "fk_catalog_catalog_types" FOREIGN KEY ("catalog_type_id") REFERENCES "catalog_types" ("id");
ALTER TABLE "catalog" ADD CONSTRAINT "fk_catalog_view_group_id" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id");
ALTER TABLE "catalog_activity" ADD CONSTRAINT "fk_catalog_activity_catalog" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id");
ALTER TABLE "catalog_categories" ADD CONSTRAINT "fk_catalog_categories_catalog" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id");
ALTER TABLE "catalog_categories" ADD CONSTRAINT "fk_catalog_categories_categories" FOREIGN KEY ("category_id") REFERENCES "content" ("id");
ALTER TABLE "content" ADD CONSTRAINT "fk_content_extension_instances" FOREIGN KEY ("extension_instance_id") REFERENCES "extension_instances" ("id");
ALTER TABLE "extension_instances" ADD CONSTRAINT "fk_extension_instances_extensions" FOREIGN KEY ("extension_id") REFERENCES "extensions" ("id");
ALTER TABLE "group_view_groups" ADD CONSTRAINT "fk_group_view_groups_groups" FOREIGN KEY ("group_id") REFERENCES "content" ("id");
ALTER TABLE "group_view_groups" ADD CONSTRAINT "fk_group_view_groups_view_groups" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id");
ALTER TABLE "site_applications" ADD CONSTRAINT "fk_site_applications_applications" FOREIGN KEY ("application_id") REFERENCES "applications" ("id");
ALTER TABLE "site_applications" ADD CONSTRAINT "fk_site_applications_sites" FOREIGN KEY ("site_id") REFERENCES "sites" ("id");
ALTER TABLE "user_applications" ADD CONSTRAINT "fk_user_applications_applications" FOREIGN KEY ("application_id") REFERENCES "applications" ("id");
ALTER TABLE "user_applications" ADD CONSTRAINT "fk_user_applications_users" FOREIGN KEY ("user_id") REFERENCES "users" ("id");
ALTER TABLE "extensions" ADD CONSTRAINT "fk_extensions_extension_sites_1" FOREIGN KEY ("extension_site_id") REFERENCES "extension_sites" ("id");
ALTER TABLE "user_activity" ADD CONSTRAINT "fk_user_activity_users_1" FOREIGN KEY ("user_id") REFERENCES "users" ("id");
ALTER TABLE "user_view_groups" ADD CONSTRAINT "fk_user_view_groups_users_1" FOREIGN KEY ("user_id") REFERENCES "users" ("id");
ALTER TABLE "user_groups" ADD CONSTRAINT "fk_user_groups_users_1" FOREIGN KEY ("user_id") REFERENCES "users" ("id");
ALTER TABLE "user_groups" ADD CONSTRAINT "fk_user_groups_content_1" FOREIGN KEY ("group_id") REFERENCES "content" ("id");
ALTER TABLE "user_view_groups" ADD CONSTRAINT "fk_user_view_groups_view_groups_1" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id");
ALTER TABLE "user_activity" ADD CONSTRAINT "fk_user_activity_catalog_1" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id");
ALTER TABLE "group_permissions" ADD CONSTRAINT "fk_group_permissions_actions_1" FOREIGN KEY ("action_id") REFERENCES "actions" ("id");
ALTER TABLE "view_group_permissions" ADD CONSTRAINT "fk_view_group_permissions_actions_1" FOREIGN KEY ("action_id") REFERENCES "actions" ("id");
ALTER TABLE "view_group_permissions" ADD CONSTRAINT "fk_view_group_permissions_view_groups_1" FOREIGN KEY ("view_group_id") REFERENCES "view_groups" ("id");
ALTER TABLE "view_group_permissions" ADD CONSTRAINT "fk_view_group_permissions_catalog_1" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id");
ALTER TABLE "group_permissions" ADD CONSTRAINT "fk_group_permissions_content_1" FOREIGN KEY ("group_id") REFERENCES "content" ("id");
ALTER TABLE "group_permissions" ADD CONSTRAINT "fk_group_permissions_catalog_1" FOREIGN KEY ("catalog_id") REFERENCES "catalog" ("id");
ALTER TABLE "site_extension_instances" ADD CONSTRAINT "fk_site_extension_instances_sites_1" FOREIGN KEY ("site_id") REFERENCES "sites" ("id");
ALTER TABLE "application_extension_instances" ADD CONSTRAINT "fk_application_extension_instances_applications_1" FOREIGN KEY ("application_id") REFERENCES "applications" ("id");
ALTER TABLE "application_extension_instances" ADD CONSTRAINT "fk_application_extension_instances_extension_instances_1" FOREIGN KEY ("extension_instance_id") REFERENCES "extension_instances" ("id");
ALTER TABLE "site_extension_instances" ADD CONSTRAINT "fk_site_extension_instances_extension_instances_1" FOREIGN KEY ("extension_instance_id") REFERENCES "extension_instances" ("id");
ALTER TABLE "catalog" ADD CONSTRAINT "fk_catalog_application_id" FOREIGN KEY ("application_id") REFERENCES "applications" ("id");

