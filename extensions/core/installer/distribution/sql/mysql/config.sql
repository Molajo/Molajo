#
# File contains default configuration and overrides for core components
#

# DEFAULT 

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 100, '', '', 0),
      (1, 0, 100, '__content', '__content', 1);

# 200 MOLAJO_EXTENSION_OPTION_ID_FIELDS

# 200 BASE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 200, '', '', 0),
      (1, 0, 200, 'alias', 'FIELD_ALIAS_LABEL', 1),
      (1, 0, 200, 'asset_type_id', 'FIELD_ASSET_TYPE_ID_LABEL', 2),
      (1, 0, 200, 'checked_out_by', 'FIELD_CHECKED_OUT_BY_LABEL', 3),
      (1, 0, 200, 'checked_out_datetime', 'FIELD_CHECKED_OUT_DATETIME_LABEL', 4),
      (1, 0, 200, 'content_text', 'FIELD_CONTENT_TEXT_LABEL', 5),
      (1, 0, 200, 'created_by', 'FIELD_CREATED_BY_LABEL', 6),
      (1, 0, 200, 'created_datetime', 'FIELD_CREATED_DATETIME_LABEL', 7),
      (1, 0, 200, 'extension_instance_id', 'FIELD_EXTENSION_EXTENSIONS_ID_LABEL', 8),
      (1, 0, 200, 'featured', 'FIELD_FEATURED_LABEL', 9),
      (1, 0, 200, 'home', 'FIELD_HOME_LABEL', 10),
      (1, 0, 200, 'id', 'FIELD_ID_LABEL', 11),
      (1, 0, 200, 'language', 'FIELD_LANGUAGE_LABEL', 12),
      (1, 0, 200, 'lft', 'FIELD_LEFT_LABEL', 13),
      (1, 0, 200, 'lvl', 'FIELD_LEVEL_LABEL', 14),
      (1, 0, 200, 'modified_by', 'FIELD_MODIFIED_BY_LABEL', 15),
      (1, 0, 200, 'modified_datetime', 'FIELD_MODIFIED_DATETIME_LABEL', 16),
      (1, 0, 200, 'ordering', 'FIELD_ORDERING_LABEL', 17),
      (1, 0, 200, 'parent_id', 'FIELD_PARENT_ID_LABEL', 18),
      (1, 0, 200, 'path', 'FIELD_PATH_LABEL', 19),
      (1, 0, 200, 'position', 'FIELD_POSITION_LABEL', 20),
      (1, 0, 200, 'protected', 'FIELD_PROTECTED_LABEL', 21),
      (1, 0, 200, 'rgt', 'FIELD_RIGHT_LABEL', 22),
      (1, 0, 200, 'root', 'FIELD_ROOT_LABEL', 23),
      (1, 0, 200, 'start_publishing_datetime', 'FIELD_START_PUBLISHING_DATETIME_LABEL', 24),
      (1, 0, 200, 'status_prior_to_version', 'FIELD_STATUS_PRIOR_TO_VERSION_LABEL', 25),
      (1, 0, 200, 'status', 'FIELD_STATUS_LABEL', 26),
      (1, 0, 200, 'stickied', 'FIELD_STICKIED_LABEL', 27),
      (1, 0, 200, 'stop_publishing_datetime', 'FIELD_STOP_PUBLISHING_DATETIME_LABEL', 28),
      (1, 0, 200, 'subtitle', 'FIELD_SUBTITLE_LABEL', 29),
      (1, 0, 200, 'title', 'FIELD_TITLE_LABEL', 30),
      (1, 0, 200, 'translation_of_id', 'FIELD_TRANSLATION_OF_ID_LABEL', 31),
      (1, 0, 200, 'version_of_id', 'FIELD_VERSION_OF_ID_LABEL', 32),
      (1, 0, 200, 'version', 'FIELD_VERSION_LABEL', 33);

# 200 Custom Fields
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 200, 'custom_fields_image1_label', 'FIELD_CUSTOM_FIELDS_IMAGE1_LABEL', 34),
      (1, 0, 200, 'custom_fields_image1_file', 'FIELD_CUSTOM_FIELDS_IMAGE1_FILE', 35),
      (1, 0, 200, 'custom_fields_image1_credit', 'FIELD_CUSTOM_FIELDS_IMAGE1_CREDIT', 36),

      (1, 0, 200, 'custom_fields_link1_label', 'FIELD_CUSTOM_FIELDS_LINK1_LABEL', 37),
      (1, 0, 200, 'custom_fields_link1_url', 'FIELD_CUSTOM_FIELDS_LINK1_FILE', 38),

      (1, 0, 200, 'custom_fields_video1_label', 'FIELD_CUSTOM_FIELDS_VIDEO1_LABEL', 39),
      (1, 0, 200, 'custom_fields_video1_url', 'FIELD_CUSTOM_FIELDS_VIDEO1_URL', 40),

      (1, 0, 200, 'custom_fields_audio1_label', 'FIELD_CUSTOM_FIELDS_AUDIO1_LABEL', 41),
      (1, 0, 200, 'custom_fields_audio1_url', 'FIELD_CUSTOM_FIELDS_AUDIO1_URL', 42),

      (1, 0, 200, 'custom_fields_file1_label', 'FIELD_CUSTOM_FIELDS_FILE1_LABEL', 43),
      (1, 0, 200, 'custom_fields_file1_url', 'FIELD_CUSTOM_FIELDS_FILE1_URL', 44);

# 200 Metadata
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 200, 'metadata_title', 'FIELD_METADATA_TITLE', 45),
      (1, 0, 200, 'metadata_author', 'FIELD_METADATA_AUTHOR', 45),
      (1, 0, 200, 'metadata_content_rights', 'FIELD_METADATA_CONTENT_RIGHTS', 46),
      (1, 0, 200, 'metadata_description', 'FIELD_METADATA_DESCRIPTION', 47),
      (1, 0, 200, 'metadata_keywords', 'FIELD_METADATA_KEYWORDS', 48),
      (1, 0, 200, 'metadata_robots', 'FIELD_METADATA_ROBOTS', 49);

# 200 Parameters
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 200, 'template_id', '', 50),
      (1, 0, 200, 'page_id', '', 51),
      (1, 0, 200, 'view_id', '', 52),
      (1, 0, 200, 'wrap_id', '', 53);

# 200 Categories
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 200, 'category_id_primary', 'FIELD_CATEGORY_ID_PRIMARY', 54),
      (1, 0, 200, 'category_id_list', 'FIELD_CATEGORY_ID_LIST', 55),
      (1, 0, 200, 'category_id_tags', 'FIELD_CATEGORY_ID_TAGS', 56);

# 200 Groups
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 200, 'group_id_list_view_action', 'FIELD_GROUP_ID_LIST_VIEW_ACTION', 57),
      (1, 0, 200, 'group_id_list_create_action', 'FIELD_GROUP_ID_LIST_CREATE_ACTION', 58),
      (1, 0, 200, 'group_id_list_edit_action', 'FIELD_GROUP_ID_LIST_EDIT_ACTION', 59),
      (1, 0, 200, 'group_id_list_publish_action', 'FIELD_GROUP_ID_LIST_PUBLISH_ACTION', 60),
      (1, 0, 200, 'group_id_list_delete_action', 'FIELD_GROUP_ID_LIST_DELETE_ACTION', 61),
      (1, 0, 200, 'group_id_list_administer_action', 'FIELD_GROUP_ID_LIST_ADMINISTER_ACTION', 62);

# 205 MOLAJO_EXTENSION_OPTION_ID_DISPLAY_ONLY_FIELDS

# 205 Assets
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 205, 'assets_id', 'FIELD_ASSETS_ID', 63),
      (1, 0, 205, 'assets_asset_type_id', 'FIELD_ASSET_TYPE_ID', 64),
      (1, 0, 205, 'assets_source_id', 'FIELD_ASSETS_SOURCE_ID', 65),
      (1, 0, 205, 'assets_routable', 'FIELD_ASSETS_ROUTABLE', 66),
      (1, 0, 205, 'assets_sef_request', 'FIELD_ASSETS_SEF_REQUEST', 67),
      (1, 0, 205, 'assets_request', 'FIELD_ASSETS_REQUEST', 68),
      (1, 0, 205, 'assets_request_option', 'FIELD_ASSETS_OPTION', 69),
      (1, 0, 205, 'assets_request_model', 'FIELD_ASSETS_MODEL', 70),
      (1, 0, 205, 'assets_redirect_to_id', 'FIELD_ASSETS_REDIRECT_TO_ID', 71),
      (1, 0, 205, 'assets_view_group_id', 'FIELD_ASSETS_VIEW_GROUP_ID', 72),
      (1, 0, 205, 'assets_primary_category_id', 'FIELD_ASSETS_PRIMARY_CATEGORY_ID', 73);

# 205 Asset Types
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 205, 'asset_types_component_option', 'FIELD_ASSET_TYPES_COMPONENT_OPTION', 76),
      (1, 0, 205, 'asset_types_id', 'FIELD_ASSET_TYPES_ID', 77),
      (1, 0, 205, 'asset_types_protected', 'FIELD_ASSET_TYPES_PROTECTED', 78),
      (1, 0, 205, 'asset_types_source_table', 'FIELD_ASSET_TYPES_SOURCE_TABLE', 79),
      (1, 0, 205, 'asset_types_title', 'FIELD_ASSET_TYPES_TITLE', 80);

# 210 MOLAJO_EXTENSION_OPTION_ID_PUBLISH_FIELDS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 210, '', '', 0),
      (1, 0, 210, 'featured', 'FIELD_FEATURED', 1),
      (1, 0, 210, 'groups_administer_action', 'FIELD_GROUPS_ADMINISTER_ACTION', 2),
      (1, 0, 210, 'groups_create_action', 'FIELD_GROUPS_CREATE_ACTION', 3),
      (1, 0, 210, 'groups_delete_action', 'FIELD_GROUPS_DELETE_ACTION', 4),
      (1, 0, 210, 'groups_edit_action', 'FIELD_GROUPS_EDIT_ACTION', 5),
      (1, 0, 210, 'groups_publish_action', 'FIELD_GROUPS_PUBLISH_ACTION', 6),
      (1, 0, 210, 'groups_view_action', 'FIELD_GROUPS_VIEW_ACTION', 7),
      (1, 0, 210, 'ordering', 'FIELD_ORDERING', 8),
      (1, 0, 210, 'start_publishing_datetime', 'FIELD_START_PUBLISHING_DATETIME', 9),
      (1, 0, 210, 'status', 'FIELD_STATUS', 10),
      (1, 0, 210, 'stickied', 'FIELD_STICKIED', 11),
      (1, 0, 210, 'stop_publishing_datetime', 'FIELD_STOP_PUBLISHING_DATETIME', 12);

# 220 MOLAJO_EXTENSION_OPTION_ID_JSON_FIELDS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 220, '', '', 0),
      (1, 0, 220, 'custom_fields', 'FIELD_JSON_CUSTOM_FIELDS', 1),
      (1, 0, 220, 'metadata', 'FIELD_JSON_METADATA', 2),
      (1, 0, 220, 'parameters', 'FIELD_JSON_PARAMETERS', 3);

# 250 MOLAJO_EXTENSION_OPTION_ID_STATUS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 250, '', '', 0),
      (1, 0, 250, '2', 'MOLAJO_OPTION_STATUS_ARCHIVED', 1),
      (1, 0, 250, '1', 'MOLAJO_OPTION_STATUS_PUBLISHED', 2),
      (1, 0, 250, '0', 'MOLAJO_OPTION_STATUS_UNPUBLISHED', 3),
      (1, 0, 250, '-1', 'MOLAJO_OPTION_STATUS_TRASHED', 4),
      (1, 0, 250, '-2', 'MOLAJO_OPTION_STATUS_SPAMMED', 5),
      (1, 0, 250, '-10', 'MOLAJO_OPTION_STATUS_VERSION', 6),
      (1, 0, 250, '-11', 'MOLAJO_OPTION_STATUS_DRAFT', 7);

# USER INTERFACE

# 300 MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_LIST
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 300, '', '', 0),
      (1, 0, 300, 'archive', 'MOLAJO_OPTION_TOOLBAR_BUTTON_ARCHIVE', 1),
      (1, 0, 300, 'checkin', 'MOLAJO_OPTION_TOOLBAR_BUTTON_CHECKIN', 2),
      (1, 0, 300, 'delete', 'MOLAJO_OPTION_TOOLBAR_BUTTON_DELETE', 3),
      (1, 0, 300, 'edit', 'MOLAJO_OPTION_TOOLBAR_BUTTON_EDIT', 4),
      (1, 0, 300, 'feature', 'MOLAJO_OPTION_TOOLBAR_BUTTON_FEATURE', 5),
      (1, 0, 300, 'help', 'MOLAJO_OPTION_TOOLBAR_BUTTON_HELP', 6),
      (1, 0, 300, 'new', 'MOLAJO_OPTION_TOOLBAR_BUTTON_NEW', 7),
      (1, 0, 300, 'publish', 'MOLAJO_OPTION_TOOLBAR_BUTTON_PUBLISH', 8),
      (1, 0, 300, 'restore', 'MOLAJO_OPTION_TOOLBAR_BUTTON_RESTORE', 9),
      (1, 0, 300, 'separator', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SEPARATOR', 10),
      (1, 0, 300, 'spam', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SPAM', 11),
      (1, 0, 300, 'sticky', 'MOLAJO_OPTION_TOOLBAR_BUTTON_STICKY', 12),
      (1, 0, 300, 'trash', 'MOLAJO_OPTION_TOOLBAR_BUTTON_TRASH', 13),
      (1, 0, 300, 'unpublish', 'MOLAJO_OPTION_TOOLBAR_BUTTON_UNPUBLISH', 14);

# 310 MOLAJO_EXTENSION_OPTION_ID_SUBMENU_LIST
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 310, '', '', 0),
      (1, 0, 310, 'default', 'MOLAJO_SUBMENU_DEFAULT', 1),
      (1, 0, 310, 'drafts', 'MOLAJO_SUBMENU_DRAFTS', 2),
      (1, 0, 310, 'featured', 'MOLAJO_SUBMENU_FEATURED', 3),
      (1, 0, 310, 'versions', 'MOLAJO_SUBMENU_VERSIONS', 4),
      (1, 0, 310, 'stickied', 'MOLAJO_SUBMENU_STICKIED', 5),
      (1, 0, 310, 'published', 'MOLAJO_SUBMENU_STICKIED', 6),
      (1, 0, 310, 'unpublished', 'MOLAJO_SUBMENU_UNPUBLISHED', 7);

# 320 MOLAJO_EXTENSION_OPTION_ID_FILTERS_LIST
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 320, '', '', 0),
      (1, 0, 320, 'alias', 'FIELD_ALIAS_LABEL', 1),
      (1, 0, 320, 'category_id_primary', 'FIELD_CATEGORY_ID_PRIMARY', 2),
      (1, 0, 320, 'category_id_list', 'FIELD_CATEGORY_ID_LIST', 3),
      (1, 0, 320, 'category_id_tags', 'FIELD_CATEGORY_ID_TAGS', 4),
      (1, 0, 320, 'created_by', 'FIELD_CREATED_BY_LABEL', 5),
      (1, 0, 320, 'created_datetime', 'FIELD_CREATED_DATETIME_LABEL', 6),
      (1, 0, 320, 'featured', 'FIELD_FEATURED_LABEL', 7),
      (1, 0, 320, 'groups_administer_action', 'FIELD_GROUPS_ADMINISTER_ACTION', 8),
      (1, 0, 320, 'groups_create_action', 'FIELD_GROUPS_CREATE_ACTION', 9),
      (1, 0, 320, 'groups_delete_action', 'FIELD_GROUPS_DELETE_ACTION', 10),
      (1, 0, 320, 'groups_edit_action', 'FIELD_GROUPS_EDIT_ACTION', 11),
      (1, 0, 320, 'groups_publish_action', 'FIELD_GROUPS_PUBLISH_ACTION', 12),
      (1, 0, 320, 'groups_view_action', 'FIELD_GROUPS_VIEW_ACTION', 13),
      (1, 0, 320, 'language', 'FIELD_LANGUAGE_LABEL', 14),
      (1, 0, 320, 'modified_by', 'FIELD_MODIFIED_BY_LABEL', 15),
      (1, 0, 320, 'modified_datetime', 'FIELD_MODIFIED_DATETIME_LABEL', 16),
      (1, 0, 320, 'path', 'FIELD_PATH_LABEL', 17),
      (1, 0, 320, 'position', 'FIELD_POSITION_LABEL', 18),
      (1, 0, 320, 'start_publishing_datetime', 'FIELD_START_PUBLISHING_DATETIME_LABEL', 19),
      (1, 0, 320, 'status', 'FIELD_STATUS_LABEL', 20),
      (1, 0, 320, 'stickied', 'FIELD_STICKIED_LABEL', 21),
      (1, 0, 320, 'stop_publishing_datetime', 'FIELD_STOP_PUBLISHING_DATETIME_LABEL', 22),
      (1, 0, 320, 'subtitle', 'FIELD_SUBTITLE_LABEL', 23),
      (1, 0, 320, 'title', 'FIELD_TITLE_LABEL', 24);

# 330 MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_EDIT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 330, '', '', 0),
      (1, 0, 330, 'apply', 'MOLAJO_OPTION_TOOLBAR_BUTTON_APPLY', 1),
      (1, 0, 330, 'close', 'MOLAJO_OPTION_TOOLBAR_BUTTON_CLOSE', 2),
      (1, 0, 330, 'help', 'MOLAJO_OPTION_TOOLBAR_BUTTON_HELP', 3),
      (1, 0, 330, 'restore', 'MOLAJO_OPTION_TOOLBAR_BUTTON_RESTORE', 4),
      (1, 0, 330, 'save', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SAVE', 5),
      (1, 0, 330, 'saveandnew', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SAVEANDNEW', 6),
      (1, 0, 330, 'saveascopy', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SAVEASCOPY', 7),
      (1, 0, 330, 'separator', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SEPARATOR', 8);

# 340 MOLAJO_EXTENSION_OPTION_ID_EDITOR_BUTTONS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 340, '', '', 0),
      (1, 0, 340, 'article', 'MANAGER_EDITOR_BUTTON_ARTICLE', 1),
      (1, 0, 340, 'audio', 'MANAGER_EDITOR_BUTTON_AUDIO', 2),
      (1, 0, 340, 'file', 'MANAGER_EDITOR_BUTTON_FILE', 3),
      (1, 0, 340, 'gallery', 'MANAGER_EDITOR_BUTTON_GALLERY', 4),
      (1, 0, 340, 'image', 'MANAGER_EDITOR_BUTTON_IMAGE', 5),
      (1, 0, 340, 'pagebreak', 'MANAGER_EDITOR_BUTTON_PAGEBREAK', 6),
      (1, 0, 340, 'readmore', 'MANAGER_EDITOR_BUTTON_READMORE', 7),
      (1, 0, 340, 'video', 'MANAGER_EDITOR_BUTTON_VIDEO', 8);

# MIME from ftp://ftp.iana.org/assignments/media-types/

# 400 MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 400, '', '', 0),
      (1, 0, 400, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1),
      (1, 0, 400, 'sp-midi', 'sp-midi', 2),
      (1, 0, 400, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3),
      (1, 0, 400, 'vnd.4SB', 'vnd.4SB', 4),
      (1, 0, 400, 'vnd.CELP', 'vnd.CELP', 5),
      (1, 0, 400, 'vnd.audiokoz', 'vnd.audiokoz', 6),
      (1, 0, 400, 'vnd.cisco.nse', 'vnd.cisco.nse', 7),
      (1, 0, 400, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8),
      (1, 0, 400, 'vnd.cns.anp1', 'vnd.cns.anp1', 9),
      (1, 0, 400, 'vnd.cns.inf1', 'vnd.cns.inf1', 10),
      (1, 0, 400, 'vnd.dece.audio', 'vnd.dece.audio', 11),
      (1, 0, 400, 'vnd.digital-winds', 'vnd.digital-winds', 12),
      (1, 0, 400, 'vnd.dlna.adts', 'vnd.dlna.adts', 13),
      (1, 0, 400, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14),
      (1, 0, 400, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15),
      (1, 0, 400, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16),
      (1, 0, 400, 'vnd.dolby.mps', 'vnd.dolby.mps', 17),
      (1, 0, 400, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18),
      (1, 0, 400, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19),
      (1, 0, 400, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20),
      (1, 0, 400, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21),
      (1, 0, 400, 'vnd.dra', 'vnd.dra', 22),
      (1, 0, 400, 'vnd.dts', 'vnd.dts', 23),
      (1, 0, 400, 'vnd.dts.hd', 'vnd.dts.hd', 24),
      (1, 0, 400, 'vnd.dvb.file', 'vnd.dvb.file', 25),
      (1, 0, 400, 'vnd.everad.plj', 'vnd.everad.plj', 26),
      (1, 0, 400, 'vnd.hns.audio', 'vnd.hns.audio', 27),
      (1, 0, 400, 'vnd.lucent.voice', 'vnd.lucent.voice', 28),
      (1, 0, 400, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29),
      (1, 0, 400, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30),
      (1, 0, 400, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31),
      (1, 0, 400, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32),
      (1, 0, 400, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33),
      (1, 0, 400, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34),
      (1, 0, 400, 'vnd.octel.sbc', 'vnd.octel.sbc', 35),
      (1, 0, 400, 'vnd.qcelp', 'vnd.qcelp', 36),
      (1, 0, 400, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37),
      (1, 0, 400, 'vnd.rip', 'vnd.rip', 38),
      (1, 0, 400, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39),
      (1, 0, 400, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);

# 410 MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 410, '', '', 0),
      (1, 0, 410, 'cgm', 'cgm', 1),
      (1, 0, 410, 'jp2', 'jp2', 2),
      (1, 0, 410, 'jpm', 'jpm', 3),
      (1, 0, 410, 'jpx', 'jpx', 4),
      (1, 0, 410, 'naplps', 'naplps', 5),
      (1, 0, 410, 'png', 'png', 6),
      (1, 0, 410, 'prs.btif', 'prs.btif', 7),
      (1, 0, 410, 'prs.pti', 'prs.pti', 8),
      (1, 0, 410, 'vnd-djvu', 'vnd-djvu', 9),
      (1, 0, 410, 'vnd-svf', 'vnd-svf', 10),
      (1, 0, 410, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11),
      (1, 0, 410, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12),
      (1, 0, 410, 'vnd.cns.inf2', 'vnd.cns.inf2', 13),
      (1, 0, 410, 'vnd.dece.graphic', 'vnd.dece.graphic', 14),
      (1, 0, 410, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15),
      (1, 0, 410, 'vnd.dwg', 'vnd.dwg', 16),
      (1, 0, 410, 'vnd.dxf', 'vnd.dxf', 17),
      (1, 0, 410, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18),
      (1, 0, 410, 'vnd.fpx', 'vnd.fpx', 19),
      (1, 0, 410, 'vnd.fst', 'vnd.fst', 20),
      (1, 0, 410, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21),
      (1, 0, 410, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22),
      (1, 0, 410, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23),
      (1, 0, 410, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24),
      (1, 0, 410, 'vnd.mix', 'vnd.mix', 25),
      (1, 0, 410, 'vnd.ms-modi', 'vnd.ms-modi', 26),
      (1, 0, 410, 'vnd.net-fpx', 'vnd.net-fpx', 27),
      (1, 0, 410, 'vnd.radiance', 'vnd.radiance', 28),
      (1, 0, 410, 'vnd.sealed-png', 'vnd.sealed-png', 29),
      (1, 0, 410, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30),
      (1, 0, 410, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31),
      (1, 0, 410, 'vnd.xiff', 'vnd.xiff', 32);

# 420 MOLAJO_EXTENSION_OPTION_ID_MIMES_TEXT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 420, '', '', 0),
      (1, 0, 420, 'n3', 'n3', 1),
      (1, 0, 420, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2),
      (1, 0, 420, 'prs.lines.tag', 'prs.lines.tag', 3),
      (1, 0, 420, 'rtf', 'rtf', 4),
      (1, 0, 420, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5),
      (1, 0, 420, 'tab-separated-values', 'tab-separated-values', 6),
      (1, 0, 420, 'turtle', 'turtle', 7),
      (1, 0, 420, 'vnd-curl', 'vnd-curl', 8),
      (1, 0, 420, 'vnd.DMClientScript', 'vnd.DMClientScript', 9),
      (1, 0, 420, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10),
      (1, 0, 420, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11),
      (1, 0, 420, 'vnd.abc', 'vnd.abc', 12),
      (1, 0, 420, 'vnd.curl', 'vnd.curl', 13),
      (1, 0, 420, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14),
      (1, 0, 420, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15),
      (1, 0, 420, 'vnd.fly', 'vnd.fly', 16),
      (1, 0, 420, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17),
      (1, 0, 420, 'vnd.graphviz', 'vnd.graphviz', 18),
      (1, 0, 420, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19),
      (1, 0, 420, 'vnd.in3d.spot', 'vnd.in3d.spot', 20),
      (1, 0, 420, 'vnd.latex-z', 'vnd.latex-z', 21),
      (1, 0, 420, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22),
      (1, 0, 420, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23),
      (1, 0, 420, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24),
      (1, 0, 420, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25),
      (1, 0, 420, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26),
      (1, 0, 420, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27),
      (1, 0, 420, 'vnd.wap-wml', 'vnd.wap-wml', 28),
      (1, 0, 420, 'vnd.wap.si', 'vnd.wap.si', 29),
      (1, 0, 420, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);

# 430 MOLAJO_EXTENSION_OPTION_ID_MIMES_VIDEO
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 430, '', '', 0),
      (1, 0, 430, 'jpm', 'jpm', 1),
      (1, 0, 430, 'mj2', 'mj2', 2),
      (1, 0, 430, 'quicktime', 'quicktime', 3),
      (1, 0, 430, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4),
      (1, 0, 430, 'vnd-mpegurl', 'vnd-mpegurl', 5),
      (1, 0, 430, 'vnd-vivo', 'vnd-vivo', 6),
      (1, 0, 430, 'vnd.CCTV', 'vnd.CCTV', 7),
      (1, 0, 430, 'vnd.dece-mp4', 'vnd.dece-mp4', 8),
      (1, 0, 430, 'vnd.dece.hd', 'vnd.dece.hd', 9),
      (1, 0, 430, 'vnd.dece.mobile', 'vnd.dece.mobile', 10),
      (1, 0, 430, 'vnd.dece.pd', 'vnd.dece.pd', 11),
      (1, 0, 430, 'vnd.dece.sd', 'vnd.dece.sd', 12),
      (1, 0, 430, 'vnd.dece.video', 'vnd.dece.video', 13),
      (1, 0, 430, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14),
      (1, 0, 430, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15),
      (1, 0, 430, 'vnd.dvb.file', 'vnd.dvb.file', 16),
      (1, 0, 430, 'vnd.fvt', 'vnd.fvt', 17),
      (1, 0, 430, 'vnd.hns.video', 'vnd.hns.video', 18),
      (1, 0, 430, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19),
      (1, 0, 430, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20),
      (1, 0, 430, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21),
      (1, 0, 430, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22),
      (1, 0, 430, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23),
      (1, 0, 430, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24),
      (1, 0, 430, 'vnd.motorola.video', 'vnd.motorola.video', 25),
      (1, 0, 430, 'vnd.motorola.videop', 'vnd.motorola.videop', 26),
      (1, 0, 430, 'vnd.mpegurl', 'vnd.mpegurl', 27),
      (1, 0, 430, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28),
      (1, 0, 430, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29),
      (1, 0, 430, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30),
      (1, 0, 430, 'vnd.objectvideo', 'vnd.objectvideo', 31),
      (1, 0, 430, 'vnd.sealed-swf', 'vnd.sealed-swf', 32),
      (1, 0, 430, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33),
      (1, 0, 430, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34),
      (1, 0, 430, 'vnd.sealed.swf', 'vnd.sealed.swf', 35),
      (1, 0, 430, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36),
      (1, 0, 430, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);

# MVC

# TASKS AND CONTROLLERS

# 1100 MOLAJO_EXTENSION_OPTION_ID_TASKS_CONTROLLER
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 1100, '', '', 0),
      (1, 0, 1100, 'add', 'display', 1),
      (1, 0, 1100, 'edit', 'display', 2),
      (1, 0, 1100, 'display', 'display', 3);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 1100, 'apply', 'edit', 4),
      (1, 0, 1100, 'cancel', 'edit', 5),
      (1, 0, 1100, 'create', 'edit', 6),
      (1, 0, 1100, 'save', 'edit', 7),
      (1, 0, 1100, 'saveascopy', 'edit', 8),
      (1, 0, 1100, 'saveandnew', 'edit', 9),
      (1, 0, 1100, 'restore', 'edit', 10);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 1100, 'archive', 'multiple', 11),
      (1, 0, 1100, 'publish', 'multiple', 12),
      (1, 0, 1100, 'unpublish', 'multiple', 13),
      (1, 0, 1100, 'spam', 'multiple', 14),
      (1, 0, 1100, 'trash', 'multiple', 15),
      (1, 0, 1100, 'feature', 'multiple', 16),
      (1, 0, 1100, 'unfeature', 'multiple', 17),
      (1, 0, 1100, 'sticky', 'multiple', 18),
      (1, 0, 1100, 'unsticky', 'multiple', 19),
      (1, 0, 1100, 'checkin', 'multiple', 20),
      (1, 0, 1100, 'reorder', 'multiple', 21),
      (1, 0, 1100, 'orderup', 'multiple', 22),
      (1, 0, 1100, 'orderdown', 'multiple', 23),
      (1, 0, 1100, 'saveorder', 'multiple', 24),
      (1, 0, 1100, 'delete', 'multiple', 25),
      (1, 0, 1100, 'copy', 'multiple', 26),
      (1, 0, 1100, 'move', 'multiple', 27);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 1100, 'login', 'login', 28),
      (1, 0, 1100, 'logout', 'login', 29);

# ACL Component Information

# 10100 MOLAJO_EXTENSION_OPTION_ID_ACL_ITEM_TESTS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 10100, '', '', 0),
      (1, 0, 10100, 'view', 'view', 1),
      (1, 0, 10100, 'create', 'create', 2),
      (1, 0, 10100, 'edit', 'edit', 3),
      (1, 0, 10100, 'publish', 'publish', 4),
      (1, 0, 10100, 'delete', 'delete', 5),
      (1, 0, 10100, 'administer', 'administer', 6);

# 10000 MOLAJO_EXTENSION_OPTION_ID_TASK_ACL_METHODS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 0, 10200, '', '', 0),
      (1, 0, 10200, 'add', 'create', 1),
      (1, 0, 10200, 'administer', 'administer', 2),
      (1, 0, 10200, 'apply', 'edit', 3),
      (1, 0, 10200, 'archive', 'publish', 4),
      (1, 0, 10200, 'cancel', '', 5),
      (1, 0, 10200, 'checkin', 'administer', 6),
      (1, 0, 10200, 'close', '', 7),
      (1, 0, 10200, 'copy', 'create', 8),
      (1, 0, 10200, 'create', 'create', 9),
      (1, 0, 10200, 'delete', 'delete', 10),
      (1, 0, 10200, 'view', 'view', 11),
      (1, 0, 10200, 'edit', 'edit', 12),
      (1, 0, 10200, 'editstate', 'publish', 13),
      (1, 0, 10200, 'feature', 'publish', 14),
      (1, 0, 10200, 'login', 'login', 15),
      (1, 0, 10200, 'logout', 'logout', 16),
      (1, 0, 10200, 'manage', 'edit', 17),
      (1, 0, 10200, 'move', 'edit', 18),
      (1, 0, 10200, 'orderdown', 'publish', 19),
      (1, 0, 10200, 'orderup', 'publish', 20),
      (1, 0, 10200, 'publish', 'publish', 21),
      (1, 0, 10200, 'reorder', 'publish', 22),
      (1, 0, 10200, 'restore', 'publish', 23),
      (1, 0, 10200, 'save', 'edit', 24),
      (1, 0, 10200, 'saveascopy', 'edit', 25),
      (1, 0, 10200, 'saveandnew', 'edit', 26),
      (1, 0, 10200, 'saveorder', 'publish', 27),
      (1, 0, 10200, 'search', 'view', 28),
      (1, 0, 10200, 'spam', 'publish', 29),
      (1, 0, 10200, 'state', 'publish', 30),
      (1, 0, 10200, 'sticky', 'publish', 31),
      (1, 0, 10200, 'trash', 'publish', 32),
      (1, 0, 10200, 'unfeature', 'publish', 33),
      (1, 0, 10200, 'unpublish', 'publish', 34),
      (1, 0, 10200, 'unsticky', 'publish', 35);
