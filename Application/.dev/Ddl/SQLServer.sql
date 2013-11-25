CREATE TABLE [actions] (
[id] int NOT NULL,
[title] varchar(255) NOT NULL DEFAULT ' ',
[protected] smallint NOT NULL DEFAULT '0',
PRIMARY KEY ([id])
)
GO

CREATE UNIQUE INDEX [idx_actions_table_title] ON [actions] ([title] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'actions',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Actions Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'actions'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Actions Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'actions'
, @level2type = 'COLUMN', @level2name = N'id'
GO

CREATE TABLE [application_extension_instances] (
[application_id] int NOT NULL,
[extension_instance_id] int NOT NULL,
PRIMARY KEY ([application_id], [extension_instance_id])
)
GO

CREATE INDEX [fk_application_extensions_applications_index] ON [application_extension_instances] ([application_id] )
GO
CREATE INDEX [fk_application_extension_instances_extension_instances_index] ON [application_extension_instances] ([extension_instance_id] )
GO

CREATE TABLE [applications] (
[id] int NOT NULL,
[catalog_type_id] int NOT NULL DEFAULT '2000',
[name] varchar(255) NOT NULL DEFAULT ' ',
[path] varchar(2048) NOT NULL DEFAULT ' ',
[description] varchar(MAX) NULL DEFAULT NULL,
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
PRIMARY KEY ([id])
)
GO

CREATE INDEX [fk_applications_catalog_types_index] ON [applications] ([catalog_type_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Application Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Application Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'catalog_type_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'name')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Application Name'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'name'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Application Name'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'name'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'path')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Application Path'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'path'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Application Path'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'path'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'description')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Application Description'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'description'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Application Description'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'description'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'customfields')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Application'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'customfields'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Application'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'customfields'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'parameters')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Application'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'parameters'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Application'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'parameters'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'applications',
'COLUMN', N'metadata')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Application'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'metadata'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Application'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'applications'
, @level2type = 'COLUMN', @level2name = N'metadata'
GO

CREATE TABLE [catalog] (
[id] int NOT NULL,
[application_id] int NOT NULL DEFAULT '0',
[catalog_type_id] int NOT NULL DEFAULT '0',
[source_id] int NOT NULL DEFAULT '0',
[enabled] smallint NOT NULL DEFAULT '0',
[redirect_to_id] int NOT NULL DEFAULT '0',
[sef_request] varchar(2048) NOT NULL DEFAULT ' ',
[page_type] varchar(255) NOT NULL,
[extension_instance_id] int NOT NULL DEFAULT '0',
[view_group_id] int NOT NULL DEFAULT '0',
[primary_category_id] int NOT NULL DEFAULT '0',
PRIMARY KEY ([id])
)
GO

CREATE UNIQUE INDEX [index_catalog_catalog_types] ON [catalog] ([application_id] , [catalog_type_id] , [source_id] , [enabled] , [redirect_to_id] , [page_type] )
GO
CREATE INDEX [sef_request] ON [catalog] ([application_id] , [enabled] , [redirect_to_id] )
GO
CREATE INDEX [index_catalog_application_id] ON [catalog] ([application_id] )
GO
CREATE INDEX [index_catalog_catalog_type_id] ON [catalog] ([catalog_type_id] )
GO
CREATE INDEX [index_catalog_view_group_id] ON [catalog] ([view_group_id] )
GO
CREATE INDEX [index_catalog_primary_category_id] ON [catalog] ([primary_category_id] )
GO
CREATE INDEX [index_catalog_extension_instance_id] ON [catalog] ([extension_instance_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'application_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Application ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'application_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Application ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'application_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'catalog_type_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'source_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Key of source data stored in table associated with Catalog Type ID Model'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'source_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Key of source data stored in table associated with Catalog Type ID Model'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'source_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'enabled')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Enabled - 1 or Disabled - 0'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'enabled'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Enabled - 1 or Disabled - 0'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'enabled'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'redirect_to_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Redirect to Catalog ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'redirect_to_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Redirect to Catalog ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'redirect_to_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'sef_request')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'SEF Request'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'sef_request'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'SEF Request'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'sef_request'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'page_type')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Menu Item Type includes such values as Item, List, or a specific Menuitem Type'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'page_type'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Menu Item Type includes such values as Item, List, or a specific Menuitem Type'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'page_type'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'extension_instance_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Instance ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'extension_instance_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Instance ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'extension_instance_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'view_group_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'View Group ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'View Group ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog',
'COLUMN', N'primary_category_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Category ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'primary_category_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Category ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog'
, @level2type = 'COLUMN', @level2name = N'primary_category_id'
GO

CREATE TABLE [catalog_activity] (
[id] int NOT NULL,
[catalog_id] int NOT NULL DEFAULT '0',
[user_id] int NOT NULL DEFAULT '0',
[action_id] int NOT NULL,
[rating] smallint NULL DEFAULT NULL,
[activity_datetime] datetime2 NULL DEFAULT NULL,
[ip_address] char(15) NOT NULL DEFAULT '',
[customfields] varchar(MAX) NULL DEFAULT NULL,
PRIMARY KEY ([id])
)
GO

CREATE INDEX [catalog_activity_catalog_index] ON [catalog_activity] ([catalog_id] )
GO

CREATE TABLE [catalog_categories] (
[catalog_id] int NOT NULL DEFAULT '0',
[category_id] int NOT NULL DEFAULT '0',
PRIMARY KEY ([catalog_id], [category_id])
)
GO

CREATE INDEX [fk_catalog_categories_catalog_index] ON [catalog_categories] ([catalog_id] )
GO
CREATE INDEX [fk_catalog_categories_categories_index] ON [catalog_categories] ([category_id] )
GO

CREATE TABLE [catalog_types] (
[id] int NOT NULL,
[primary_category_id] int NOT NULL,
[title] varchar(255) NOT NULL,
[alias] varchar(255) NOT NULL,
[model_type] varchar(255) NOT NULL,
[model_name] varchar(255) NOT NULL,
[protected] smallint NOT NULL DEFAULT '0',
PRIMARY KEY ([id])
)
GO

CREATE UNIQUE INDEX [title] ON [catalog_types] ([title] )
GO
CREATE UNIQUE INDEX [alias] ON [catalog_types] ([alias] )
GO
CREATE UNIQUE INDEX [model_name] ON [catalog_types] ([model_name] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog_types',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Types Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Types Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog_types',
'COLUMN', N'primary_category_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Category ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'primary_category_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Category ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'primary_category_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog_types',
'COLUMN', N'title')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'title'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'title'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog_types',
'COLUMN', N'alias')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'alias'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'alias'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog_types',
'COLUMN', N'model_type')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type Model Type'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'model_type'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type Model Type'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'model_type'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog_types',
'COLUMN', N'model_name')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type Model Name'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'model_name'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type Model Name'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'model_name'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'catalog_types',
'COLUMN', N'protected')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Protected from system removal'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'protected'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Protected from system removal'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'catalog_types'
, @level2type = 'COLUMN', @level2name = N'protected'
GO

CREATE TABLE [content] (
[id] int NOT NULL,
[site_id] int NOT NULL DEFAULT '0',
[extension_instance_id] int NOT NULL DEFAULT '0',
[catalog_type_id] int NOT NULL DEFAULT '0',
[title] varchar(255) NOT NULL DEFAULT ' ',
[subtitle] varchar(255) NOT NULL DEFAULT ' ',
[path] varchar(2048) NOT NULL DEFAULT ' ',
[alias] varchar(255) NOT NULL DEFAULT ' ',
[content_text] varchar(MAX) NULL DEFAULT NULL,
[protected] smallint NOT NULL DEFAULT '0',
[featured] smallint NOT NULL DEFAULT '0',
[stickied] smallint NOT NULL DEFAULT '0',
[status] smallint NOT NULL DEFAULT '0',
[start_publishing_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[stop_publishing_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[version] int NOT NULL DEFAULT '1',
[version_of_id] int NOT NULL DEFAULT '0',
[status_prior_to_version] int NOT NULL DEFAULT '0',
[created_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[created_by] int NOT NULL DEFAULT '0',
[modified_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[modified_by] int NOT NULL DEFAULT '0',
[checked_out_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[checked_out_by] int NOT NULL DEFAULT '0',
[root] int NOT NULL DEFAULT '0',
[parent_id] int NOT NULL DEFAULT '0',
[lft] int NOT NULL DEFAULT '0',
[rgt] int NOT NULL DEFAULT '0',
[lvl] int NOT NULL DEFAULT '0',
[home] smallint NOT NULL DEFAULT '0',
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
[language] char(7) NOT NULL DEFAULT 'en-GB',
[translation_of_id] int NOT NULL DEFAULT '0',
[ordering] int NOT NULL DEFAULT '0',
PRIMARY KEY ([id])
)
GO

CREATE UNIQUE INDEX [alias] ON [content] ([catalog_type_id] , [alias] )
GO
CREATE INDEX [fk_content_extension_instance_id] ON [content] ([extension_instance_id] )
GO
CREATE INDEX [fk_content_catalog_type_id] ON [content] ([catalog_type_id] )
GO
CREATE INDEX [fk_content_site_id] ON [content] ([site_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Content Table Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Content Table Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'site_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Site Primary Key or 0'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'site_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Site Primary Key or 0'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'site_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'extension_instance_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Instance Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'extension_instance_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Instance Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'extension_instance_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'catalog_type_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'title')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'title'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'title'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'subtitle')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'subtitle'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'subtitle'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'path')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'URI Path to append to Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'path'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'URI Path to append to Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'path'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'alias')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Slug, or alias, associated with Title, must be unique when combined with path.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'alias'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Slug, or alias, associated with Title, must be unique when combined with path.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'alias'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'content_text')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Text field'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'content_text'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Text field'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'content_text'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'protected')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'protected'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'protected'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'featured')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'featured'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'featured'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'stickied')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'stickied'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'stickied'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'status')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'status'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'status'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'start_publishing_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'stop_publishing_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'version')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'version_of_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'status_prior_to_version')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'created_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'created_by')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'created_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'created_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'modified_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'modified_by')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'modified_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'modified_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'checked_out_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'checked_out_by')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'root')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'root'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'root'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'parent_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'parent_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'parent_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'lft')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'lft'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'lft'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'rgt')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'rgt'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'rgt'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'lvl')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'lvl'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'lvl'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'customfields')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'customfields'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'customfields'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'parameters')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'parameters'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'parameters'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'metadata')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'metadata'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'metadata'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'content',
'COLUMN', N'ordering')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'ordering'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'content'
, @level2type = 'COLUMN', @level2name = N'ordering'
GO

CREATE TABLE [extension_instances] (
[id] int NOT NULL,
[extension_id] int NOT NULL,
[catalog_type_id] int NOT NULL,
[title] varchar(255) NOT NULL DEFAULT ' ',
[subtitle] varchar(255) NOT NULL DEFAULT ' ',
[path] varchar(2048) NOT NULL DEFAULT ' ',
[alias] varchar(255) NOT NULL DEFAULT ' ',
[menu] varchar(255) NOT NULL,
[page_type] varchar(255) NOT NULL,
[content_text] varchar(MAX) NULL DEFAULT NULL,
[protected] smallint NOT NULL DEFAULT '0',
[featured] smallint NOT NULL DEFAULT '0',
[stickied] smallint NOT NULL DEFAULT '0',
[status] smallint NOT NULL DEFAULT '0',
[start_publishing_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[stop_publishing_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[version] int NOT NULL DEFAULT '1',
[version_of_id] int NOT NULL DEFAULT '0',
[status_prior_to_version] int NOT NULL DEFAULT '0',
[created_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[created_by] int NOT NULL DEFAULT '0',
[modified_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[modified_by] int NOT NULL DEFAULT '0',
[checked_out_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[checked_out_by] int NOT NULL DEFAULT '0',
[root] int NOT NULL DEFAULT '0',
[parent_id] int NOT NULL DEFAULT '0',
[lft] int NOT NULL DEFAULT '0',
[rgt] int NOT NULL DEFAULT '0',
[lvl] int NOT NULL DEFAULT '0',
[home] smallint NOT NULL DEFAULT '0',
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
[language] char(7) NOT NULL DEFAULT 'en-GB',
[translation_of_id] int NOT NULL DEFAULT '0',
[ordering] int NOT NULL DEFAULT '0',
PRIMARY KEY ([id])
)
GO

CREATE INDEX [fk_extension_instances_extensions_index] ON [extension_instances] ([extension_id] )
GO
CREATE INDEX [fk_extension_instances_catalog_type_index] ON [extension_instances] ([catalog_type_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Instance Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Instance Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'extension_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'extension_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'extension_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'catalog_type_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'title')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'title'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'title'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'subtitle')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'subtitle'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'subtitle'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'path')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Path prepended to alias to create URL'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'path'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Path prepended to alias to create URL'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'path'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'alias')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'URI Alias of Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'alias'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'URI Alias of Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'alias'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'menu')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'For Menuitem content types, contains the name of the associated Menu'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'menu'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'For Menuitem content types, contains the name of the associated Menu'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'menu'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'page_type')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'For Menuitem content types, contains the name of the associated Menuitem Type'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'page_type'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'For Menuitem content types, contains the name of the associated Menuitem Type'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'page_type'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'content_text')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Information about the Extension'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'content_text'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Information about the Extension'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'content_text'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'protected')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'protected'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'protected'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'featured')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'featured'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'featured'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'stickied')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'stickied'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'stickied'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'status')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'status'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'status'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'start_publishing_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'stop_publishing_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'version')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'version_of_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'status_prior_to_version')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'created_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'created_by')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'created_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'created_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'modified_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'modified_by')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'modified_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'modified_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'checked_out_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'checked_out_by')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'root')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'root'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'root'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'parent_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'parent_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'parent_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'lft')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'lft'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'lft'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'rgt')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'rgt'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'rgt'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'lvl')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'lvl'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'lvl'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'customfields')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'customfields'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'customfields'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'parameters')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'parameters'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'parameters'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'metadata')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'metadata'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'metadata'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extension_instances',
'COLUMN', N'ordering')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'ordering'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extension_instances'
, @level2type = 'COLUMN', @level2name = N'ordering'
GO

CREATE TABLE [extension_sites] (
[id] int NOT NULL,
[name] varchar(255) NULL DEFAULT ' ',
[enabled] smallint NOT NULL DEFAULT '0',
[location] varchar(2048) NOT NULL,
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
PRIMARY KEY ([id])
)
GO

CREATE TABLE [extensions] (
[id] int NOT NULL,
[extension_site_id] int NOT NULL DEFAULT '0',
[catalog_type_id] int NOT NULL,
[name] char(255) NOT NULL DEFAULT ' ',
[subtitle] char(255) NOT NULL DEFAULT ' ',
[language] char(7) NOT NULL DEFAULT 'en-GB',
[translation_of_id] int NOT NULL DEFAULT '0',
[ordering] int NOT NULL DEFAULT '0',
PRIMARY KEY ([id])
)
GO

CREATE INDEX [extensions_extension_sites_index] ON [extensions] ([extension_site_id] )
GO
CREATE INDEX [fk_extension_catalog_type_index] ON [extensions] ([catalog_type_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extensions',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extensions',
'COLUMN', N'extension_site_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Site ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'extension_site_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Site ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'extension_site_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extensions',
'COLUMN', N'catalog_type_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extensions',
'COLUMN', N'name')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Name of Extension'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'name'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Name of Extension'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'name'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extensions',
'COLUMN', N'subtitle')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Subtitle (Yes, I know it has no title.)'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'subtitle'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Subtitle (Yes, I know it has no title.)'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'subtitle'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extensions',
'COLUMN', N'translation_of_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'This data is a translation for this the data with this primary key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'translation_of_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'This data is a translation for this the data with this primary key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'translation_of_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'extensions',
'COLUMN', N'ordering')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'ordering'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'extensions'
, @level2type = 'COLUMN', @level2name = N'ordering'
GO

CREATE TABLE [group_permissions] (
[id] int NOT NULL,
[group_id] int NOT NULL,
[catalog_id] int NOT NULL,
[action_id] int NOT NULL,
PRIMARY KEY ([id])
)
GO

CREATE INDEX [fk_group_permissions_actions_index] ON [group_permissions] ([action_id] )
GO
CREATE INDEX [fk_group_permissions_content_index] ON [group_permissions] ([group_id] )
GO
CREATE INDEX [fk_group_permissions_catalog_index] ON [group_permissions] ([catalog_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'group_permissions',
'COLUMN', N'group_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to #_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_permissions'
, @level2type = 'COLUMN', @level2name = N'group_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to #_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_permissions'
, @level2type = 'COLUMN', @level2name = N'group_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'group_permissions',
'COLUMN', N'catalog_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_catalog.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_permissions'
, @level2type = 'COLUMN', @level2name = N'catalog_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_catalog.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_permissions'
, @level2type = 'COLUMN', @level2name = N'catalog_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'group_permissions',
'COLUMN', N'action_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_actions.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_permissions'
, @level2type = 'COLUMN', @level2name = N'action_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_actions.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_permissions'
, @level2type = 'COLUMN', @level2name = N'action_id'
GO

CREATE TABLE [group_view_groups] (
[group_id] int NOT NULL,
[view_group_id] int NOT NULL,
PRIMARY KEY ([view_group_id], [group_id])
)
GO

CREATE INDEX [fk_group_view_groups_view_groups_index] ON [group_view_groups] ([view_group_id] )
GO
CREATE INDEX [fk_group_view_groups_groups_index] ON [group_view_groups] ([group_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'group_view_groups',
'COLUMN', N'group_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'FK to the molajo_group table.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_view_groups'
, @level2type = 'COLUMN', @level2name = N'group_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'FK to the molajo_group table.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_view_groups'
, @level2type = 'COLUMN', @level2name = N'group_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'group_view_groups',
'COLUMN', N'view_group_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'FK to the molajo_groupings table.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_view_groups'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'FK to the molajo_groupings table.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'group_view_groups'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
GO

CREATE TABLE [log] (
[id] int NOT NULL,
[priority] int NULL DEFAULT NULL,
[message] varchar(MAX) NULL DEFAULT NULL,
[date] datetime2 NULL DEFAULT NULL,
[category] varchar(255) NULL DEFAULT NULL,
[customfields] varchar(MAX) NULL,
PRIMARY KEY ([id])
)
GO

CREATE INDEX [idx_category_date_priority] ON [log] ([category] , [date] , [priority] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'log',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Log Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'log'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Log Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'log'
, @level2type = 'COLUMN', @level2name = N'id'
GO

CREATE TABLE [sessions] (
[session_id] varchar(255) NOT NULL,
[application_id] int NOT NULL,
[session_time] datetime2 NULL,
[data] varchar(MAX) NULL DEFAULT NULL,
[user_id] int NULL DEFAULT '0',
PRIMARY KEY ([session_id])
)
GO

CREATE INDEX [fk_sessions_applications_index] ON [sessions] ([application_id] )
GO

CREATE TABLE [site_applications] (
[application_id] int NOT NULL,
[site_id] int NOT NULL,
PRIMARY KEY ([site_id], [application_id])
)
GO

CREATE INDEX [fk_site_applications_sites_index] ON [site_applications] ([site_id] )
GO
CREATE INDEX [fk_site_applications_applications_index] ON [site_applications] ([application_id] )
GO

CREATE TABLE [site_extension_instances] (
[site_id] int NOT NULL,
[extension_instance_id] int NOT NULL,
PRIMARY KEY ([site_id], [extension_instance_id])
)
GO

CREATE INDEX [fk_application_extensions_sites_index] ON [site_extension_instances] ([site_id] )
GO
CREATE INDEX [fk_application_extension_instances_extension_instances_index] ON [site_extension_instances] ([extension_instance_id] )
GO

CREATE TABLE [sites] (
[id] int NOT NULL,
[catalog_type_id] int NOT NULL DEFAULT '1000',
[name] varchar(255) NOT NULL DEFAULT ' ',
[path] varchar(2048) NOT NULL DEFAULT ' ',
[base_url] varchar(2048) NOT NULL DEFAULT 'Used only as documentation',
[description] varchar(MAX) NULL DEFAULT NULL,
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
PRIMARY KEY ([id])
)
GO

IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Site Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Site Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'catalog_type_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'name')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Name of Extension'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'name'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Name of Extension'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'name'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'path')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Path for this site within the Sites Folder'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'path'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Path for this site within the Sites Folder'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'path'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'description')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Site Description'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'description'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Site Description'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'description'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'customfields')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Site'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'customfields'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Site'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'customfields'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'parameters')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Site'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'parameters'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Site'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'parameters'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'sites',
'COLUMN', N'metadata')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Site'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'metadata'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Site'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'sites'
, @level2type = 'COLUMN', @level2name = N'metadata'
GO

CREATE TABLE [user_activity] (
[id] int NOT NULL,
[user_id] int NOT NULL DEFAULT '0',
[action_id] int NOT NULL DEFAULT '0',
[catalog_id] int NOT NULL DEFAULT '0',
[activity_datetime] datetime2 NULL DEFAULT NULL,
[ip_address] varchar(15) NOT NULL DEFAULT '',
PRIMARY KEY ([id])
)
GO

CREATE INDEX [user_activity_user_index] ON [user_activity] ([user_id] )
GO
CREATE INDEX [user_activity_catalog_index] ON [user_activity] ([catalog_id] )
GO
CREATE INDEX [user_activity_action_index] ON [user_activity] ([action_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_activity',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'User Activity Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'User Activity Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_activity',
'COLUMN', N'user_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'User ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'user_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'User ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'user_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_activity',
'COLUMN', N'action_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Action ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'action_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Action ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'action_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_activity',
'COLUMN', N'catalog_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'catalog_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'catalog_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_activity',
'COLUMN', N'activity_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Activity Datetime'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'activity_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Activity Datetime'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'activity_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_activity',
'COLUMN', N'ip_address')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'IP Address'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'ip_address'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'IP Address'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_activity'
, @level2type = 'COLUMN', @level2name = N'ip_address'
GO

CREATE TABLE [user_applications] (
[user_id] int NOT NULL,
[application_id] int NOT NULL,
PRIMARY KEY ([application_id], [user_id])
)
GO

CREATE INDEX [fk_user_applications_users_index] ON [user_applications] ([user_id] )
GO
CREATE INDEX [fk_user_applications_applications_index] ON [user_applications] ([application_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_applications',
'COLUMN', N'user_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'User ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_applications'
, @level2type = 'COLUMN', @level2name = N'user_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'User ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_applications'
, @level2type = 'COLUMN', @level2name = N'user_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_applications',
'COLUMN', N'application_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Application ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_applications'
, @level2type = 'COLUMN', @level2name = N'application_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Application ID Foreign Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_applications'
, @level2type = 'COLUMN', @level2name = N'application_id'
GO

CREATE TABLE [user_groups] (
[user_id] int NOT NULL,
[group_id] int NOT NULL,
PRIMARY KEY ([group_id], [user_id])
)
GO

CREATE INDEX [fk_molajo_user_groups_molajo_users_index] ON [user_groups] ([user_id] )
GO
CREATE INDEX [fk_molajo_user_groups_molajo_groups_index] ON [user_groups] ([group_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_groups',
'COLUMN', N'user_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_users.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_groups'
, @level2type = 'COLUMN', @level2name = N'user_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_users.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_groups'
, @level2type = 'COLUMN', @level2name = N'user_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_groups',
'COLUMN', N'group_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_groups'
, @level2type = 'COLUMN', @level2name = N'group_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_groups'
, @level2type = 'COLUMN', @level2name = N'group_id'
GO

CREATE TABLE [user_view_groups] (
[user_id] int NOT NULL,
[view_group_id] int NOT NULL,
PRIMARY KEY ([view_group_id], [user_id])
)
GO

CREATE INDEX [fk_user_groups_users_index] ON [user_view_groups] ([user_id] )
GO
CREATE INDEX [fk_user_view_groups_view_groups_index] ON [user_view_groups] ([view_group_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_view_groups',
'COLUMN', N'user_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_users.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_view_groups'
, @level2type = 'COLUMN', @level2name = N'user_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_users.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_view_groups'
, @level2type = 'COLUMN', @level2name = N'user_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'user_view_groups',
'COLUMN', N'view_group_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_view_groups'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'user_view_groups'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
GO

CREATE TABLE [users] (
[id] int NOT NULL,
[site_id] int NOT NULL DEFAULT '0',
[catalog_type_id] int NOT NULL DEFAULT '0',
[username] varchar(255) NOT NULL,
[first_name] varchar(100) NULL DEFAULT '',
[last_name] varchar(150) NULL DEFAULT '',
[full_name] varchar(255) NOT NULL,
[alias] varchar(255) NOT NULL,
[content_text] varchar(MAX) NULL DEFAULT NULL,
[email] varchar(255) NULL DEFAULT '  ',
[password] varchar(100) NOT NULL DEFAULT '  ',
[block] smallint NOT NULL DEFAULT '0',
[register_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[activation_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[last_visit_datetime] datetime2 NOT NULL DEFAULT '0000-00-00 00:00:00',
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
PRIMARY KEY ([id])
)
GO

CREATE UNIQUE INDEX [username] ON [users] ([username] )
GO
CREATE UNIQUE INDEX [email] ON [users] ([email] )
GO
CREATE INDEX [last_name_first_name] ON [users] ([last_name] , [first_name] )
GO
CREATE INDEX [fk_users_sites_index] ON [users] ([site_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Key for Users'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Key for Users'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'site_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Site ID Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'site_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Site ID Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'site_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'catalog_type_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'username')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Username'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'username'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Username'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'username'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'first_name')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'First name of User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'first_name'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'First name of User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'first_name'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'last_name')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Last name of User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'last_name'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Last name of User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'last_name'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'full_name')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Full name of User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'full_name'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Full name of User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'full_name'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'alias')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'User alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'alias'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'User alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'alias'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'content_text')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Text for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'content_text'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Text for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'content_text'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'email')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Email address of user'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'email'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Email address of user'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'email'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'password')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'User password'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'password'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'User password'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'password'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'block')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'If activiated, blocks user from logging on'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'block'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'If activiated, blocks user from logging on'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'block'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'register_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Registered date for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'register_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Registered date for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'register_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'activation_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Activation date for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'activation_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Activation date for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'activation_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'last_visit_datetime')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Last visit date for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'last_visit_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Last visit date for User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'last_visit_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'customfields')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'customfields'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'customfields'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'parameters')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'parameters'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'parameters'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'users',
'COLUMN', N'metadata')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'metadata'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this User'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'users'
, @level2type = 'COLUMN', @level2name = N'metadata'
GO

CREATE TABLE [view_group_permissions] (
[id] int NOT NULL,
[view_group_id] int NOT NULL,
[catalog_id] int NOT NULL,
[action_id] int NOT NULL,
PRIMARY KEY ([id])
)
GO

CREATE INDEX [fk_view_group_permissions_view_groups_index] ON [view_group_permissions] ([view_group_id] )
GO
CREATE INDEX [fk_view_group_permissions_actions_index] ON [view_group_permissions] ([action_id] )
GO
CREATE INDEX [fk_view_group_permissions_catalog_index] ON [view_group_permissions] ([catalog_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'view_group_permissions',
'COLUMN', N'view_group_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'view_group_permissions'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_groups.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'view_group_permissions'
, @level2type = 'COLUMN', @level2name = N'view_group_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'view_group_permissions',
'COLUMN', N'catalog_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_catalog.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'view_group_permissions'
, @level2type = 'COLUMN', @level2name = N'catalog_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_catalog.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'view_group_permissions'
, @level2type = 'COLUMN', @level2name = N'catalog_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description',
'SCHEMA', N'',
'TABLE', N'view_group_permissions',
'COLUMN', N'action_id')) > 0)
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_actions.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'view_group_permissions'
, @level2type = 'COLUMN', @level2name = N'action_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Foreign Key to molajo_actions.id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'view_group_permissions'
, @level2type = 'COLUMN', @level2name = N'action_id'
GO

CREATE TABLE [view_groups] (
[id] int NOT NULL,
[view_group_name_list] varchar(MAX) NOT NULL,
[view_group_id_list] varchar(MAX) NOT NULL,
PRIMARY KEY ([id])
)
GO


ALTER TABLE [catalog] ADD CONSTRAINT [fk_catalog_catalog_types] FOREIGN KEY ([catalog_type_id]) REFERENCES [catalog_types] ([id])
GO
ALTER TABLE [catalog] ADD CONSTRAINT [fk_catalog_view_group_id] FOREIGN KEY ([view_group_id]) REFERENCES [view_groups] ([id])
GO
ALTER TABLE [catalog_activity] ADD CONSTRAINT [fk_catalog_activity_catalog] FOREIGN KEY ([catalog_id]) REFERENCES [catalog] ([id])
GO
ALTER TABLE [catalog_categories] ADD CONSTRAINT [fk_catalog_categories_catalog] FOREIGN KEY ([catalog_id]) REFERENCES [catalog] ([id])
GO
ALTER TABLE [catalog_categories] ADD CONSTRAINT [fk_catalog_categories_categories] FOREIGN KEY ([category_id]) REFERENCES [content] ([id])
GO
ALTER TABLE [content] ADD CONSTRAINT [fk_content_extension_instances] FOREIGN KEY ([extension_instance_id]) REFERENCES [extension_instances] ([id])
GO
ALTER TABLE [extension_instances] ADD CONSTRAINT [fk_extension_instances_extensions] FOREIGN KEY ([extension_id]) REFERENCES [extensions] ([id])
GO
ALTER TABLE [group_view_groups] ADD CONSTRAINT [fk_group_view_groups_groups] FOREIGN KEY ([group_id]) REFERENCES [content] ([id])
GO
ALTER TABLE [group_view_groups] ADD CONSTRAINT [fk_group_view_groups_view_groups] FOREIGN KEY ([view_group_id]) REFERENCES [view_groups] ([id])
GO
ALTER TABLE [site_applications] ADD CONSTRAINT [fk_site_applications_applications] FOREIGN KEY ([application_id]) REFERENCES [applications] ([id])
GO
ALTER TABLE [site_applications] ADD CONSTRAINT [fk_site_applications_sites] FOREIGN KEY ([site_id]) REFERENCES [sites] ([id])
GO
ALTER TABLE [user_applications] ADD CONSTRAINT [fk_user_applications_applications] FOREIGN KEY ([application_id]) REFERENCES [applications] ([id])
GO
ALTER TABLE [user_applications] ADD CONSTRAINT [fk_user_applications_users] FOREIGN KEY ([user_id]) REFERENCES [users] ([id])
GO
ALTER TABLE [extensions] ADD CONSTRAINT [fk_extensions_extension_sites_1] FOREIGN KEY ([extension_site_id]) REFERENCES [extension_sites] ([id])
GO
ALTER TABLE [user_activity] ADD CONSTRAINT [fk_user_activity_users_1] FOREIGN KEY ([user_id]) REFERENCES [users] ([id])
GO
ALTER TABLE [user_view_groups] ADD CONSTRAINT [fk_user_view_groups_users_1] FOREIGN KEY ([user_id]) REFERENCES [users] ([id])
GO
ALTER TABLE [user_groups] ADD CONSTRAINT [fk_user_groups_users_1] FOREIGN KEY ([user_id]) REFERENCES [users] ([id])
GO
ALTER TABLE [user_groups] ADD CONSTRAINT [fk_user_groups_content_1] FOREIGN KEY ([group_id]) REFERENCES [content] ([id])
GO
ALTER TABLE [user_view_groups] ADD CONSTRAINT [fk_user_view_groups_view_groups_1] FOREIGN KEY ([view_group_id]) REFERENCES [view_groups] ([id])
GO
ALTER TABLE [user_activity] ADD CONSTRAINT [fk_user_activity_catalog_1] FOREIGN KEY ([catalog_id]) REFERENCES [catalog] ([id])
GO
ALTER TABLE [group_permissions] ADD CONSTRAINT [fk_group_permissions_actions_1] FOREIGN KEY ([action_id]) REFERENCES [actions] ([id])
GO
ALTER TABLE [view_group_permissions] ADD CONSTRAINT [fk_view_group_permissions_actions_1] FOREIGN KEY ([action_id]) REFERENCES [actions] ([id])
GO
ALTER TABLE [view_group_permissions] ADD CONSTRAINT [fk_view_group_permissions_view_groups_1] FOREIGN KEY ([view_group_id]) REFERENCES [view_groups] ([id])
GO
ALTER TABLE [view_group_permissions] ADD CONSTRAINT [fk_view_group_permissions_catalog_1] FOREIGN KEY ([catalog_id]) REFERENCES [catalog] ([id])
GO
ALTER TABLE [group_permissions] ADD CONSTRAINT [fk_group_permissions_content_1] FOREIGN KEY ([group_id]) REFERENCES [content] ([id])
GO
ALTER TABLE [group_permissions] ADD CONSTRAINT [fk_group_permissions_catalog_1] FOREIGN KEY ([catalog_id]) REFERENCES [catalog] ([id])
GO
ALTER TABLE [site_extension_instances] ADD CONSTRAINT [fk_site_extension_instances_sites_1] FOREIGN KEY ([site_id]) REFERENCES [sites] ([id])
GO
ALTER TABLE [application_extension_instances] ADD CONSTRAINT [fk_application_extension_instances_applications_1] FOREIGN KEY ([application_id]) REFERENCES [applications] ([id])
GO
ALTER TABLE [application_extension_instances] ADD CONSTRAINT [fk_application_extension_instances_extension_instances_1] FOREIGN KEY ([extension_instance_id]) REFERENCES [extension_instances] ([id])
GO
ALTER TABLE [site_extension_instances] ADD CONSTRAINT [fk_site_extension_instances_extension_instances_1] FOREIGN KEY ([extension_instance_id]) REFERENCES [extension_instances] ([id])
GO
ALTER TABLE [catalog] ADD CONSTRAINT [fk_catalog_application_id] FOREIGN KEY ([application_id]) REFERENCES [applications] ([id])
GO

