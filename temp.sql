DROP INDEX [idx_actions_table_title] ON [actions]
GO
DROP INDEX [fk_application_extensions_applications_index] ON [application_extension_instances]
GO
DROP INDEX [fk_application_extension_instances_extension_instances_index] ON [application_extension_instances]
GO
DROP INDEX [fk_applications_catalog_types_index] ON [applications]
GO
DROP INDEX [index_catalog_catalog_types] ON [catalog]
GO
DROP INDEX [sef_request] ON [catalog]
GO
DROP INDEX [index_catalog_application_id] ON [catalog]
GO
DROP INDEX [index_catalog_catalog_type_id] ON [catalog]
GO
DROP INDEX [index_catalog_view_group_id] ON [catalog]
GO
DROP INDEX [index_catalog_primary_category_id] ON [catalog]
GO
DROP INDEX [index_catalog_extension_instance_id] ON [catalog]
GO
DROP INDEX [catalog_activity_catalog_index] ON [catalog_activity]
GO
DROP INDEX [fk_catalog_categories_catalog_index] ON [catalog_categories]
GO
DROP INDEX [fk_catalog_categories_categories_index] ON [catalog_categories]
GO
DROP INDEX [title] ON [catalog_types]
GO
DROP INDEX [alias] ON [catalog_types]
GO
DROP INDEX [model_name] ON [catalog_types]
GO
DROP INDEX [alias] ON [content]
GO
DROP INDEX [fk_content_extension_instance_id] ON [content]
GO
DROP INDEX [fk_content_catalog_type_id] ON [content]
GO
DROP INDEX [fk_content_site_id] ON [content]
GO
DROP INDEX [fk_extension_instances_extensions_index] ON [extension_instances]
GO
DROP INDEX [fk_extension_instances_catalog_type_index] ON [extension_instances]
GO
DROP INDEX [extensions_extension_sites_index] ON [extensions]
GO
DROP INDEX [fk_extension_catalog_type_index] ON [extensions]
GO
DROP INDEX [fk_group_permissions_actions_index] ON [group_permissions]
GO
DROP INDEX [fk_group_permissions_content_index] ON [group_permissions]
GO
DROP INDEX [fk_group_permissions_catalog_index] ON [group_permissions]
GO
DROP INDEX [fk_group_view_groups_view_groups_index] ON [group_view_groups]
GO
DROP INDEX [fk_group_view_groups_groups_index] ON [group_view_groups]
GO
DROP INDEX [idx_category_date_priority] ON [log]
GO
DROP INDEX [fk_sessions_applications_index] ON [sessions]
GO
DROP INDEX [fk_site_applications_sites_index] ON [site_applications]
GO
DROP INDEX [fk_site_applications_applications_index] ON [site_applications]
GO
DROP INDEX [fk_application_extensions_sites_index] ON [site_extension_instances]
GO
DROP INDEX [fk_application_extension_instances_extension_instances_index] ON [site_extension_instances]
GO
DROP INDEX [user_activity_user_index] ON [user_activity]
GO
DROP INDEX [user_activity_catalog_index] ON [user_activity]
GO
DROP INDEX [user_activity_action_index] ON [user_activity]
GO
DROP INDEX [fk_user_applications_users_index] ON [user_applications]
GO
DROP INDEX [fk_user_applications_applications_index] ON [user_applications]
GO
DROP INDEX [fk_molajo_user_groups_molajo_users_index] ON [user_groups]
GO
DROP INDEX [fk_molajo_user_groups_molajo_groups_index] ON [user_groups]
GO
DROP INDEX [fk_user_groups_users_index] ON [user_view_groups]
GO
DROP INDEX [fk_user_view_groups_view_groups_index] ON [user_view_groups]
GO
DROP INDEX [username] ON [users]
GO
DROP INDEX [email] ON [users]
GO
DROP INDEX [last_name_first_name] ON [users]
GO
DROP INDEX [fk_users_sites_index] ON [users]
GO
DROP INDEX [fk_view_group_permissions_view_groups_index] ON [view_group_permissions]
GO
DROP INDEX [fk_view_group_permissions_actions_index] ON [view_group_permissions]
GO
DROP INDEX [fk_view_group_permissions_catalog_index] ON [view_group_permissions]
GO
DROP INDEX [title_language] ON [language_strings]
GO
DROP INDEX [path_alias] ON [language_strings]
GO
DROP INDEX [fk_language_strings_extension_instance_id] ON [language_strings]
GO
DROP INDEX [fk_language_strings_catalog_type_id] ON [language_strings]
GO

ALTER TABLE [actions]DROP CONSTRAINT []
GO
ALTER TABLE [application_extension_instances]DROP CONSTRAINT []
GO
ALTER TABLE [applications]DROP CONSTRAINT []
GO
ALTER TABLE [catalog]DROP CONSTRAINT []
GO
ALTER TABLE [catalog_activity]DROP CONSTRAINT []
GO
ALTER TABLE [catalog_categories]DROP CONSTRAINT []
GO
ALTER TABLE [catalog_types]DROP CONSTRAINT []
GO
ALTER TABLE [content]DROP CONSTRAINT []
GO
ALTER TABLE [extension_instances]DROP CONSTRAINT []
GO
ALTER TABLE [extension_sites]DROP CONSTRAINT []
GO
ALTER TABLE [extensions]DROP CONSTRAINT []
GO
ALTER TABLE [group_permissions]DROP CONSTRAINT []
GO
ALTER TABLE [group_view_groups]DROP CONSTRAINT []
GO
ALTER TABLE [log]DROP CONSTRAINT []
GO
ALTER TABLE [sessions]DROP CONSTRAINT []
GO
ALTER TABLE [site_applications]DROP CONSTRAINT []
GO
ALTER TABLE [site_extension_instances]DROP CONSTRAINT []
GO
ALTER TABLE [sites]DROP CONSTRAINT []
GO
ALTER TABLE [user_activity]DROP CONSTRAINT []
GO
ALTER TABLE [user_applications]DROP CONSTRAINT []
GO
ALTER TABLE [user_groups]DROP CONSTRAINT []
GO
ALTER TABLE [user_view_groups]DROP CONSTRAINT []
GO
ALTER TABLE [users]DROP CONSTRAINT []
GO
ALTER TABLE [view_group_permissions]DROP CONSTRAINT []
GO
ALTER TABLE [view_groups]DROP CONSTRAINT []
GO
ALTER TABLE [groups]DROP CONSTRAINT []
GO
ALTER TABLE [language_strings]DROP CONSTRAINT []
GO

DROP TABLE [groups]
GO
DROP TABLE [language_strings]
GO

CREATE TABLE [groups] (
[id] int NOT NULL,
[title] varchar(255) NOT NULL DEFAULT ' ',
[subtitle] varchar(255) NOT NULL DEFAULT ' ',
[path] varchar(2048) NOT NULL DEFAULT ' ',
[alias] varchar(255) NOT NULL DEFAULT ' ',
[content_text] varchar(MAX) NULL DEFAULT NULL,
[protected] tinyint NOT NULL DEFAULT '0',
[featured] tinyint NOT NULL DEFAULT '0',
[stickied] tinyint NOT NULL DEFAULT '0',
[status] tinyint NOT NULL DEFAULT '0',
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
[home] tinyint NOT NULL DEFAULT '0',
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
[language] char(7) NOT NULL DEFAULT 'en-GB',
[translation_of_id] int NOT NULL DEFAULT '0',
[ordering] int NOT NULL DEFAULT '0',
PRIMARY KEY ([id]) 
)
GO

IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Groups Table Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Groups Table Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'title')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'title'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'title'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'subtitle')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'subtitle'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'subtitle'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'path')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'URI Path to append to Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'path'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'URI Path to append to Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'path'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'alias')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Slug, or alias, associated with Title, must be unique when combined with path.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'alias'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Slug, or alias, associated with Title, must be unique when combined with path.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'alias'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'content_text')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Text field'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'content_text'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Text field'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'content_text'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'protected')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'protected'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'protected'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'featured')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'featured'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'featured'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'stickied')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'stickied'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'stickied'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'status')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'status'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'status'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'start_publishing_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'stop_publishing_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'version')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'version_of_id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'status_prior_to_version')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'created_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'created_by')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'created_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'created_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'modified_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'modified_by')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'modified_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'modified_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'checked_out_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'checked_out_by')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'root')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'root'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'root'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'parent_id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'parent_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'parent_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'lft')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'lft'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'lft'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'rgt')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'rgt'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'rgt'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'lvl')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'lvl'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'lvl'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'customfields')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'customfields'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'customfields'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'parameters')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'parameters'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'parameters'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'metadata')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'metadata'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'metadata'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'groups', 
'COLUMN', N'ordering')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'ordering'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'groups'
, @level2type = 'COLUMN', @level2name = N'ordering'
GO

CREATE TABLE [language_strings] (
[id] int NOT NULL,
[site_id] int NOT NULL DEFAULT '0',
[extension_instance_id] int NOT NULL DEFAULT '0',
[catalog_type_id] int NOT NULL DEFAULT '0',
[title] varchar(255) NOT NULL DEFAULT ' ',
[subtitle] varchar(255) NOT NULL DEFAULT ' ',
[path] varchar(2048) NOT NULL DEFAULT ' ',
[alias] varchar(255) NOT NULL DEFAULT ' ',
[content_text] varchar(MAX) NULL DEFAULT NULL,
[protected] tinyint NOT NULL DEFAULT '0',
[featured] tinyint NOT NULL DEFAULT '0',
[stickied] tinyint NOT NULL DEFAULT '0',
[status] tinyint NOT NULL DEFAULT '0',
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
[home] tinyint NOT NULL DEFAULT '0',
[customfields] varchar(MAX) NULL DEFAULT NULL,
[parameters] varchar(MAX) NULL DEFAULT NULL,
[metadata] varchar(MAX) NULL DEFAULT NULL,
[language] char(7) NOT NULL DEFAULT 'en-GB',
[translation_of_id] int NOT NULL DEFAULT '0',
[ordering] int NOT NULL DEFAULT '0',
PRIMARY KEY ([id]) 
)
GO

CREATE UNIQUE INDEX [title_language] ON [language_strings] ([title] , [language] )
GO
CREATE UNIQUE INDEX [path_alias] ON [language_strings] ([path] , [alias] , [title] )
GO
CREATE INDEX [fk_language_strings_extension_instance_id] ON [language_strings] ([extension_instance_id] )
GO
CREATE INDEX [fk_language_strings_catalog_type_id] ON [language_strings] ([catalog_type_id] )
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Language String Table Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Language String Table Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'site_id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Site Primary Key or 0'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'site_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Site Primary Key or 0'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'site_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'extension_instance_id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Extension Instance Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'extension_instance_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Extension Instance Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'extension_instance_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'catalog_type_id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Catalog Type Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Catalog Type Primary Key'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'catalog_type_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'title')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'title'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Title'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'title'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'subtitle')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'subtitle'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Subtitle'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'subtitle'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'path')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'URI Path to append to Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'path'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'URI Path to append to Alias'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'path'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'alias')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Slug, or alias, associated with Title, must be unique when combined with path.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'alias'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Slug, or alias, associated with Title, must be unique when combined with path.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'alias'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'content_text')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Text field'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'content_text'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Text field'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'content_text'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'protected')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'protected'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'If activated, represents an important feature required for operations that cannot be removed.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'protected'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'featured')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'featured'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Featured. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'featured'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'stickied')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'stickied'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Indicator representing content designated as Stickied. Can be used in queries.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'stickied'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'status')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'status'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'status'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'start_publishing_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish Begin Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'start_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'stop_publishing_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Publish End Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'stop_publishing_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'version')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Version Number'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'version_of_id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Primary Key for this Version'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'version_of_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'status_prior_to_version')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'State value prior to creating this version, can be used to determine if content was just published'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'status_prior_to_version'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'created_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'created_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'created_by')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'created_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Created by User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'created_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'modified_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified Date'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'modified_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'modified_by')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'modified_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Modified By User ID'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'modified_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'checked_out_datetime')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out Date and Time'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'checked_out_datetime'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'checked_out_by')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Checked out by User Id'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'checked_out_by'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'root')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'root'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the root node for the tree'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'root'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'parent_id')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'parent_id'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Used with Hierarchical Data to indicate the parent for this node.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'parent_id'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'lft')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'lft'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which increases from the root node in sequential order until the lowest branch is reached.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'lft'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'rgt')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'rgt'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'rgt'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'lvl')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'lvl'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Number representing the heirarchical level of the content. The number one is the first level. '
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'lvl'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'customfields')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'customfields'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Fields for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'customfields'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'parameters')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'parameters'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Custom Parameters for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'parameters'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'metadata')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'metadata'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Metadata definitions for this Resource Item'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'metadata'
GO
IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
'SCHEMA', N'', 
'TABLE', N'language_strings', 
'COLUMN', N'ordering')) > 0) 
EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'ordering'
ELSE
EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'Ordering'
, @level0type = 'SCHEMA', @level0name = N''
, @level1type = 'TABLE', @level1name = N'language_strings'
, @level2type = 'COLUMN', @level2name = N'ordering'
GO

