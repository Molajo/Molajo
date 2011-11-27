#
# File contains default configuration and overrides for core components
#

# DEFAULT 

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 100, '', '', 0),
      (1, 100, '__content', '__content', 1);

# 200 MOLAJO_EXTENSION_OPTION_ID_FIELDS

# 200 BASE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, '', '', 0),
      (1, 200, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 1),
      (1, 200, 'asset_type_id', 'MOLAJO_FIELD_ASSET_TYPE_ID_LABEL', 2),
      (1, 200, 'checked_out_by', 'MOLAJO_FIELD_CHECKED_OUT_BY_LABEL', 3),
      (1, 200, 'checked_out_datetime', 'MOLAJO_FIELD_CHECKED_OUT_DATETIME_LABEL', 4),
      (1, 200, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 5),
      (1, 200, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 6),
      (1, 200, 'created_datetime', 'MOLAJO_FIELD_CREATED_DATETIME_LABEL', 7),
      (1, 200, 'extension_instance_id', 'MOLAJO_FIELD_EXTENSION_INSTANCE_ID_LABEL', 8),
      (1, 200, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 9),
      (1, 200, 'home', 'MOLAJO_FIELD_HOME_LABEL', 10),
      (1, 200, 'id', 'MOLAJO_FIELD_ID_LABEL', 11),
      (1, 200, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 12),
      (1, 200, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 13),
      (1, 200, 'lvl', 'MOLAJO_FIELD_LVL_LABEL', 14),
      (1, 200, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 15),
      (1, 200, 'modified_datetime', 'MOLAJO_FIELD_MODIFIED_DATETIME_LABEL', 16),
      (1, 200, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 17),
      (1, 200, 'parent_id', 'MOLAJO_FIELD_PARENT_ID_LABEL', 18),
      (1, 200, 'path', 'MOLAJO_FIELD_PATH_LABEL', 19),
      (1, 200, 'position', 'MOLAJO_FIELD_POSITION_LABEL', 20),
      (1, 200, 'protected', 'MOLAJO_FIELD_PROTECTED_LABEL', 21),
      (1, 200, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 22),
      (1, 200, 'root', 'MOLAJO_FIELD_ROOT_LABEL', 23),
      (1, 200, 'start_publishing_datetime', 'MOLAJO_FIELD_START_PUBLISHING_DATETIME_LABEL', 24),
      (1, 200, 'status_prior_to_version', 'MOLAJO_FIELD_STATUS_PRIOR_TO_VERSION_LABEL', 25),
      (1, 200, 'status', 'MOLAJO_FIELD_STATUS_LABEL', 26),
      (1, 200, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 27),
      (1, 200, 'stop_publishing_datetime', 'MOLAJO_FIELD_STOP_PUBLISHING_DATETIME_LABEL', 28),
      (1, 200, 'subtitle', 'MOLAJO_FIELD_SUBTITLE_LABEL', 29),
      (1, 200, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 30),
      (1, 200, 'translation_of_id', 'MOLAJO_FIELD_TRANSLATION_OF_ID_LABEL', 31),
      (1, 200, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 32),
      (1, 200, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 33);

# 200 Custom Fields
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'custom_fields_image1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_LABEL', 34),
      (1, 200, 'custom_fields_image1_file', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_FILE', 35),
      (1, 200, 'custom_fields_image1_credit', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_CREDIT', 36),

      (1, 200, 'custom_fields_link1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK1_LABEL', 37),
      (1, 200, 'custom_fields_link1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK1_FILE', 38),

      (1, 200, 'custom_fields_video1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_VIDEO1_LABEL', 39),
      (1, 200, 'custom_fields_video1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_VIDEO1_URL', 40),

      (1, 200, 'custom_fields_audio1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_AUDIO1_LABEL', 41),
      (1, 200, 'custom_fields_audio1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_AUDIO1_URL', 42),

      (1, 200, 'custom_fields_file1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE1_LABEL', 43),
      (1, 200, 'custom_fields_file1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE1_URL', 44);

# 200 Metadata
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'metadata_author', 'MOLAJO_FIELD_METADATA_AUTHOR', 45),
      (1, 200, 'metadata_content_rights', 'MOLAJO_FIELD_METADATA_CONTENT_RIGHTS', 46),
      (1, 200, 'metadata_description', 'MOLAJO_FIELD_METADATA_DESCRIPTION', 47),
      (1, 200, 'metadata_keywords', 'MOLAJO_FIELD_METADATA_KEYWORDS', 48),
      (1, 200, 'metadata_robots', 'MOLAJO_FIELD_METADATA_ROBOTS', 49);

# 200 Parameters
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'parameter_template_id_item', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_ID_ITEM', 50),
      (1, 200, 'parameter_template_page_item', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_PAGE_ITEM', 51),
      (1, 200, 'parameter_layout_item', 'MOLAJO_FIELD_PARAMETERS_LAYOUT_ITEM', 52),
            
      (1, 200, 'parameter_template_id_items', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_ID_ITEM', 53),
      (1, 200, 'parameter_template_page_items', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_PAGE_ITEM', 54),
      (1, 200, 'parameter_layout_items', 'MOLAJO_FIELD_PARAMETERS_LAYOUT_ITEMS', 55);

# 200 Categories
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'category_id_primary', 'MOLAJO_FIELD_CATEGORY_ID_PRIMARY', 56),
      (1, 200, 'category_id_list', 'MOLAJO_FIELD_CATEGORY_ID_LIST', 57),
      (1, 200, 'category_id_tags', 'MOLAJO_FIELD_CATEGORY_ID_TAGS', 58);

# 200 Groups
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'group_id_list_view_action', 'MOLAJO_FIELD_GROUP_ID_LIST_VIEW_ACTION', 59),
      (1, 200, 'group_id_list_create_action', 'MOLAJO_FIELD_GROUP_ID_LIST_CREATE_ACTION', 60),
      (1, 200, 'group_id_list_edit_action', 'MOLAJO_FIELD_GROUP_ID_LIST_EDIT_ACTION', 61),
      (1, 200, 'group_id_list_publish_action', 'MOLAJO_FIELD_GROUP_ID_LIST_PUBLISH_ACTION', 62),
      (1, 200, 'group_id_list_delete_action', 'MOLAJO_FIELD_GROUP_ID_LIST_DELETE_ACTION', 63),
      (1, 200, 'group_id_list_administer_action', 'MOLAJO_FIELD_GROUP_ID_LIST_ADMINISTER_ACTION', 64);

# 205 MOLAJO_EXTENSION_OPTION_ID_DISPLAY_ONLY_FIELDS

# 205 Assets
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 205, 'assets_id', 'MOLAJO_FIELD_ASSETS_ID', 65),
      (1, 205, 'assets_asset_type_id', 'MOLAJO_FIELD_ASSET_TYPE_ID', 66),
      (1, 205, 'assets_source_id', 'MOLAJO_FIELD_ASSETS_SOURCE_ID', 67),
      (1, 205, 'assets_title', 'MOLAJO_FIELD_ASSETS_TITLE', 68),
      (1, 205, 'assets_sef_request', 'MOLAJO_FIELD_ASSETS_SEF_REQUEST', 69),
      (1, 205, 'assets_request', 'MOLAJO_FIELD_ASSETS_REQUEST', 70),
      (1, 205, 'assets_primary_category_id', 'MOLAJO_FIELD_ASSETS_PRIMARY_CATEGORY_ID', 71),
      (1, 205, 'assets_template_id', 'MOLAJO_FIELD_ASSETS_TEMPLATE_ID', 72),
      (1, 205, 'assets_template_page', 'MOLAJO_FIELD_ASSETS_TEMPLATE_PAGE', 73),
      (1, 205, 'assets_language', 'MOLAJO_FIELD_ASSETS_LANGUAGE', 74),
      (1, 205, 'assets_translation_of_id', 'MOLAJO_FIELD_TRANSLATION_OF_ID', 75),
      (1, 205, 'assets_redirect_to_id', 'MOLAJO_FIELD_ASSETS_REDIRECT_TO_ID', 76),
      (1, 205, 'assets_view_group_id', 'MOLAJO_FIELD_ASSETS_VIEW_GROUP_ID', 77);

# 205 Asset Types
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 205, 'asset_types_component_option', 'MOLAJO_FIELD_ASSET_TYPES_COMPONENT_OPTION', 78);
      (1, 205, 'asset_types_id', 'MOLAJO_FIELD_ASSET_TYPES_ID', 79),
      (1, 205, 'asset_types_protected', 'MOLAJO_FIELD_ASSET_TYPES_PROTECTED', 80),
      (1, 205, 'asset_types_source_table', 'MOLAJO_FIELD_ASSET_TYPES_SOURCE_TABLE', 81),
      (1, 205, 'asset_types_title', 'MOLAJO_FIELD_ASSET_TYPES_TITLE', 82);

# 210 MOLAJO_EXTENSION_OPTION_ID_PUBLISH_FIELDS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 210, '', '', 0),
      (1, 210, 'featured', 'MOLAJO_FIELD_FEATURED', 1),
      (1, 210, 'groups_administer_action', 'MOLAJO_FIELD_GROUPS_ADMINISTER_ACTION', 2),
      (1, 210, 'groups_create_action', 'MOLAJO_FIELD_GROUPS_CREATE_ACTION', 3),
      (1, 210, 'groups_delete_action', 'MOLAJO_FIELD_GROUPS_DELETE_ACTION', 4),
      (1, 210, 'groups_edit_action', 'MOLAJO_FIELD_GROUPS_EDIT_ACTION', 5),
      (1, 210, 'groups_publish_action', 'MOLAJO_FIELD_GROUPS_PUBLISH_ACTION', 6),
      (1, 210, 'groups_view_action', 'MOLAJO_FIELD_GROUPS_VIEW_ACTION', 7),
      (1, 210, 'ordering', 'MOLAJO_FIELD_ORDERING', 8),
      (1, 210, 'start_publishing_datetime', 'MOLAJO_FIELD_START_PUBLISHING_DATETIME', 9),
      (1, 210, 'status', 'MOLAJO_FIELD_STATUS', 10),
      (1, 210, 'stickied', 'MOLAJO_FIELD_STICKIED', 11),
      (1, 210, 'stop_publishing_datetime', 'MOLAJO_FIELD_STOP_PUBLISHING_DATETIME', 12);

# 220 MOLAJO_EXTENSION_OPTION_ID_JSON_FIELDS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 220, '', '', 0),
      (1, 220, 'custom_fields', 'MOLAJO_FIELD_JSON_CUSTOM_FIELDS', 1),
      (1, 220, 'metadata', 'MOLAJO_FIELD_JSON_METADATA', 2),
      (1, 220, 'parameters', 'MOLAJO_FIELD_JSON_PARAMETERS', 3);

# 250 MOLAJO_EXTENSION_OPTION_ID_STATUS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 250, '', '', 0),
      (1, 250, '2', 'MOLAJO_OPTION_STATUS_ARCHIVED', 1),
      (1, 250, '1', 'MOLAJO_OPTION_STATUS_PUBLISHED', 2),
      (1, 250, '0', 'MOLAJO_OPTION_STATUS_UNPUBLISHED', 3),
      (1, 250, '-1', 'MOLAJO_OPTION_STATUS_TRASHED', 4),
      (1, 250, '-2', 'MOLAJO_OPTION_STATUS_SPAMMED', 5),
      (1, 250, '-10', 'MOLAJO_OPTION_STATUS_VERSION', 6),
      (1, 250, '-11', 'MOLAJO_OPTION_STATUS_DRAFT', 7);

# USER INTERFACE

# 300 MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_LIST
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 300, '', '', 0),
      (1, 300, 'archive', 'MOLAJO_OPTION_TOOLBAR_BUTTON_ARCHIVE', 1),
      (1, 300, 'checkin', 'MOLAJO_OPTION_TOOLBAR_BUTTON_CHECKIN', 2),
      (1, 300, 'delete', 'MOLAJO_OPTION_TOOLBAR_BUTTON_DELETE', 3),
      (1, 300, 'edit', 'MOLAJO_OPTION_TOOLBAR_BUTTON_EDIT', 4),
      (1, 300, 'feature', 'MOLAJO_OPTION_TOOLBAR_BUTTON_FEATURE', 5),
      (1, 300, 'help', 'MOLAJO_OPTION_TOOLBAR_BUTTON_HELP', 6),
      (1, 300, 'new', 'MOLAJO_OPTION_TOOLBAR_BUTTON_NEW', 7),
      (1, 300, 'publish', 'MOLAJO_OPTION_TOOLBAR_BUTTON_PUBLISH', 8),
      (1, 300, 'restore', 'MOLAJO_OPTION_TOOLBAR_BUTTON_RESTORE', 9),
      (1, 300, 'separator', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SEPARATOR', 10),
      (1, 300, 'spam', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SPAM', 11),
      (1, 300, 'sticky', 'MOLAJO_OPTION_TOOLBAR_BUTTON_STICKY', 12),
      (1, 300, 'trash', 'MOLAJO_OPTION_TOOLBAR_BUTTON_TRASH', 13),
      (1, 300, 'unpublish', 'MOLAJO_OPTION_TOOLBAR_BUTTON_UNPUBLISH', 14);

# 310 MOLAJO_EXTENSION_OPTION_ID_SUBMENU_LIST
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 310, '', '', 0),
      (1, 310, 'default', 'MANAGER_SUBMENU_DEFAULT', 1),
      (1, 310, 'drafts', 'MANAGER_SUBMENU_DRAFTS', 2),
      (1, 310, 'featured', 'MANAGER_SUBMENU_FEATURED', 3),
      (1, 310, 'versions', 'MANAGER_SUBMENU_VERSIONS', 4),
      (1, 310, 'stickied', 'MANAGER_SUBMENU_STICKIED', 5),
      (1, 310, 'unpublished', 'MANAGER_SUBMENU_UNPUBLISHED', 6);

# 320 MOLAJO_EXTENSION_OPTION_ID_FILTERS_LIST
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 320, '', '', 0),
      (1, 320, 'access', 'MANAGER_OPTION_FILTER_ACCESS', 1),
      (1, 320, 'alias', 'MANAGER_OPTION_FILTER_ALIAS', 2),
      (1, 320, 'category', 'MANAGER_OPTION_FILTER_CATEGORY', 4),
      (1, 320, 'created_by', 'MANAGER_OPTION_FILTER_AUTHOR', 3),
      (1, 320, 'created', 'MANAGER_OPTION_FILTER_CREATE_DATE', 6),
      (1, 320, 'featured', 'MANAGER_OPTION_FILTER_FEATURED', 7),
      (1, 320, 'groups_administer_action', 'MOLAJO_FIELD_GROUPS_ADMINISTER_ACTION', 2),
      (1, 320, 'groups_create_action', 'MOLAJO_FIELD_GROUPS_CREATE_ACTION', 3),
      (1, 320, 'groups_delete_action', 'MOLAJO_FIELD_GROUPS_DELETE_ACTION', 4),
      (1, 320, 'groups_edit_action', 'MOLAJO_FIELD_GROUPS_EDIT_ACTION', 5),
      (1, 320, 'groups_publish_action', 'MOLAJO_FIELD_GROUPS_PUBLISH_ACTION', 6),
      (1, 320, 'groups_view_action', 'MOLAJO_FIELD_GROUPS_VIEW_ACTION', 7),
      (1, 320, 'language', 'MANAGER_OPTION_FILTER_LANGUAGE', 9),
      (1, 320, 'modified', 'MANAGER_OPTION_FILTER_UPDATE_DATE', 10),
      (1, 320, 'start_publishing_datetime', 'MANAGER_OPTION_FILTER_PUBLISH_DATE', 11),
      (1, 320, 'status', 'MANAGER_OPTION_FILTER_STATUS', 12),
      (1, 320, 'stickied', 'MANAGER_OPTION_FILTER_STICKIED', 13),
      (1, 320, 'subtitle', 'MANAGER_OPTION_FILTER_SUBTITLE', 15);
      (1, 320, 'title', 'MANAGER_OPTION_FILTER_TITLE', 14),



# 330 MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_EDIT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 330, '', '', 0),
      (1, 330, 'apply', 'MOLAJO_OPTION_TOOLBAR_BUTTON_APPLY', 1),
      (1, 330, 'close', 'MOLAJO_OPTION_TOOLBAR_BUTTON_CLOSE', 2),
      (1, 330, 'help', 'MOLAJO_OPTION_TOOLBAR_BUTTON_HELP', 3),
      (1, 330, 'restore', 'MOLAJO_OPTION_TOOLBAR_BUTTON_RESTORE', 4),
      (1, 330, 'save', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SAVE', 5),
      (1, 330, 'saveandnew', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SAVEANDNEW', 6),
      (1, 330, 'saveascopy', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SAVEASCOPY', 7),
      (1, 330, 'separator', 'MOLAJO_OPTION_TOOLBAR_BUTTON_SEPARATOR', 8);

# 340 MOLAJO_EXTENSION_OPTION_ID_EDITOR_BUTTONS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 340, '', '', 0),
      (1, 340, 'article', 'MANAGER_EDITOR_BUTTON_ARTICLE', 1),
      (1, 340, 'audio', 'MANAGER_EDITOR_BUTTON_AUDIO', 2),
      (1, 340, 'file', 'MANAGER_EDITOR_BUTTON_FILE', 3),
      (1, 340, 'gallery', 'MANAGER_EDITOR_BUTTON_GALLERY', 4),
      (1, 340, 'image', 'MANAGER_EDITOR_BUTTON_IMAGE', 5),
      (1, 340, 'pagebreak', 'MANAGER_EDITOR_BUTTON_PAGEBREAK', 6),
      (1, 340, 'readmore', 'MANAGER_EDITOR_BUTTON_READMORE', 7),
      (1, 340, 'video', 'MANAGER_EDITOR_BUTTON_VIDEO', 8);

# MIME from ftp://ftp.iana.org/assignments/media-types/

# 400 MOLAJO_EXTENSION_OPTION_ID_AUDIO_MIMES
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 400, '', '', 0),
      (1, 400, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1),
      (1, 400, 'sp-midi', 'sp-midi', 2),
      (1, 400, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3),
      (1, 400, 'vnd.4SB', 'vnd.4SB', 4),
      (1, 400, 'vnd.CELP', 'vnd.CELP', 5),
      (1, 400, 'vnd.audiokoz', 'vnd.audiokoz', 6),
      (1, 400, 'vnd.cisco.nse', 'vnd.cisco.nse', 7),
      (1, 400, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8),
      (1, 400, 'vnd.cns.anp1', 'vnd.cns.anp1', 9),
      (1, 400, 'vnd.cns.inf1', 'vnd.cns.inf1', 10),
      (1, 400, 'vnd.dece.audio', 'vnd.dece.audio', 11),
      (1, 400, 'vnd.digital-winds', 'vnd.digital-winds', 12),
      (1, 400, 'vnd.dlna.adts', 'vnd.dlna.adts', 13),
      (1, 400, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14),
      (1, 400, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15),
      (1, 400, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16),
      (1, 400, 'vnd.dolby.mps', 'vnd.dolby.mps', 17),
      (1, 400, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18),
      (1, 400, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19),
      (1, 400, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20),
      (1, 400, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21),
      (1, 400, 'vnd.dra', 'vnd.dra', 22),
      (1, 400, 'vnd.dts', 'vnd.dts', 23),
      (1, 400, 'vnd.dts.hd', 'vnd.dts.hd', 24),
      (1, 400, 'vnd.dvb.file', 'vnd.dvb.file', 25),
      (1, 400, 'vnd.everad.plj', 'vnd.everad.plj', 26),
      (1, 400, 'vnd.hns.audio', 'vnd.hns.audio', 27),
      (1, 400, 'vnd.lucent.voice', 'vnd.lucent.voice', 28),
      (1, 400, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29),
      (1, 400, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30),
      (1, 400, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31),
      (1, 400, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32),
      (1, 400, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33),
      (1, 400, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34),
      (1, 400, 'vnd.octel.sbc', 'vnd.octel.sbc', 35),
      (1, 400, 'vnd.qcelp', 'vnd.qcelp', 36),
      (1, 400, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37),
      (1, 400, 'vnd.rip', 'vnd.rip', 38),
      (1, 400, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39),
      (1, 400, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);

# 410 MOLAJO_EXTENSION_OPTION_ID_IMAGE_MIMES
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 410, '', '', 0),
      (1, 410, 'cgm', 'cgm', 1),
      (1, 410, 'jp2', 'jp2', 2),
      (1, 410, 'jpm', 'jpm', 3),
      (1, 410, 'jpx', 'jpx', 4),
      (1, 410, 'naplps', 'naplps', 5),
      (1, 410, 'png', 'png', 6),
      (1, 410, 'prs.btif', 'prs.btif', 7),
      (1, 410, 'prs.pti', 'prs.pti', 8),
      (1, 410, 'vnd-djvu', 'vnd-djvu', 9),
      (1, 410, 'vnd-svf', 'vnd-svf', 10),
      (1, 410, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11),
      (1, 410, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12),
      (1, 410, 'vnd.cns.inf2', 'vnd.cns.inf2', 13),
      (1, 410, 'vnd.dece.graphic', 'vnd.dece.graphic', 14),
      (1, 410, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15),
      (1, 410, 'vnd.dwg', 'vnd.dwg', 16),
      (1, 410, 'vnd.dxf', 'vnd.dxf', 17),
      (1, 410, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18),
      (1, 410, 'vnd.fpx', 'vnd.fpx', 19),
      (1, 410, 'vnd.fst', 'vnd.fst', 20),
      (1, 410, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21),
      (1, 410, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22),
      (1, 410, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23),
      (1, 410, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24),
      (1, 410, 'vnd.mix', 'vnd.mix', 25),
      (1, 410, 'vnd.ms-modi', 'vnd.ms-modi', 26),
      (1, 410, 'vnd.net-fpx', 'vnd.net-fpx', 27),
      (1, 410, 'vnd.radiance', 'vnd.radiance', 28),
      (1, 410, 'vnd.sealed-png', 'vnd.sealed-png', 29),
      (1, 410, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30),
      (1, 410, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31),
      (1, 410, 'vnd.xiff', 'vnd.xiff', 32);

# 420 MOLAJO_EXTENSION_OPTION_ID_TEXT_MIMES
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 420, '', '', 0),
      (1, 420, 'n3', 'n3', 1),
      (1, 420, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2),
      (1, 420, 'prs.lines.tag', 'prs.lines.tag', 3),
      (1, 420, 'rtf', 'rtf', 4),
      (1, 420, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5),
      (1, 420, 'tab-separated-values', 'tab-separated-values', 6),
      (1, 420, 'turtle', 'turtle', 7),
      (1, 420, 'vnd-curl', 'vnd-curl', 8),
      (1, 420, 'vnd.DMClientScript', 'vnd.DMClientScript', 9),
      (1, 420, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10),
      (1, 420, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11),
      (1, 420, 'vnd.abc', 'vnd.abc', 12),
      (1, 420, 'vnd.curl', 'vnd.curl', 13),
      (1, 420, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14),
      (1, 420, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15),
      (1, 420, 'vnd.fly', 'vnd.fly', 16),
      (1, 420, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17),
      (1, 420, 'vnd.graphviz', 'vnd.graphviz', 18),
      (1, 420, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19),
      (1, 420, 'vnd.in3d.spot', 'vnd.in3d.spot', 20),
      (1, 420, 'vnd.latex-z', 'vnd.latex-z', 21),
      (1, 420, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22),
      (1, 420, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23),
      (1, 420, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24),
      (1, 420, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25),
      (1, 420, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26),
      (1, 420, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27),
      (1, 420, 'vnd.wap-wml', 'vnd.wap-wml', 28),
      (1, 420, 'vnd.wap.si', 'vnd.wap.si', 29),
      (1, 420, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);

# 430 MOLAJO_EXTENSION_OPTION_ID_VIDEO_MIMES
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 430, '', '', 0),
      (1, 430, 'jpm', 'jpm', 1),
      (1, 430, 'mj2', 'mj2', 2),
      (1, 430, 'quicktime', 'quicktime', 3),
      (1, 430, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4),
      (1, 430, 'vnd-mpegurl', 'vnd-mpegurl', 5),
      (1, 430, 'vnd-vivo', 'vnd-vivo', 6),
      (1, 430, 'vnd.CCTV', 'vnd.CCTV', 7),
      (1, 430, 'vnd.dece-mp4', 'vnd.dece-mp4', 8),
      (1, 430, 'vnd.dece.hd', 'vnd.dece.hd', 9),
      (1, 430, 'vnd.dece.mobile', 'vnd.dece.mobile', 10),
      (1, 430, 'vnd.dece.pd', 'vnd.dece.pd', 11),
      (1, 430, 'vnd.dece.sd', 'vnd.dece.sd', 12),
      (1, 430, 'vnd.dece.video', 'vnd.dece.video', 13),
      (1, 430, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14),
      (1, 430, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15),
      (1, 430, 'vnd.dvb.file', 'vnd.dvb.file', 16),
      (1, 430, 'vnd.fvt', 'vnd.fvt', 17),
      (1, 430, 'vnd.hns.video', 'vnd.hns.video', 18),
      (1, 430, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19),
      (1, 430, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20),
      (1, 430, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21),
      (1, 430, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22),
      (1, 430, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23),
      (1, 430, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24),
      (1, 430, 'vnd.motorola.video', 'vnd.motorola.video', 25),
      (1, 430, 'vnd.motorola.videop', 'vnd.motorola.videop', 26),
      (1, 430, 'vnd.mpegurl', 'vnd.mpegurl', 27),
      (1, 430, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28),
      (1, 430, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29),
      (1, 430, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30),
      (1, 430, 'vnd.objectvideo', 'vnd.objectvideo', 31),
      (1, 430, 'vnd.sealed-swf', 'vnd.sealed-swf', 32),
      (1, 430, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33),
      (1, 430, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34),
      (1, 430, 'vnd.sealed.swf', 'vnd.sealed.swf', 35),
      (1, 430, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36),
      (1, 430, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);

# MVC

# CONTROLLER TASKS

# 1100 MOLAJO_EXTENSION_OPTION_ID_TASK_TO_CONTROLLER
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1100, '', '', 0),
      (1, 1100, 'add', 'display', 1),
      (1, 1100, 'edit', 'display', 2),
      (1, 1100, 'display', 'display', 3);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1100, 'apply', 'edit', 4),
      (1, 1100, 'cancel', 'edit', 5),
      (1, 1100, 'create', 'edit', 6),
      (1, 1100, 'save', 'edit', 7),
      (1, 1100, 'saveascopy', 'edit', 8),
      (1, 1100, 'saveandnew', 'edit', 9),
      (1, 1100, 'restore', 'edit', 10);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1100, 'archive', 'multiple', 11),
      (1, 1100, 'publish', 'multiple', 12),
      (1, 1100, 'unpublish', 'multiple', 13),
      (1, 1100, 'spam', 'multiple', 14),
      (1, 1100, 'trash', 'multiple', 15),
      (1, 1100, 'feature', 'multiple', 16),
      (1, 1100, 'unfeature', 'multiple', 17),
      (1, 1100, 'sticky', 'multiple', 18),
      (1, 1100, 'unsticky', 'multiple', 19),
      (1, 1100, 'checkin', 'multiple', 20),
      (1, 1100, 'reorder', 'multiple', 21),
      (1, 1100, 'orderup', 'multiple', 22),
      (1, 1100, 'orderdown', 'multiple', 23),
      (1, 1100, 'saveorder', 'multiple', 24),
      (1, 1100, 'delete', 'multiple', 25),
      (1, 1100, 'copy', 'multiple', 26),
      (1, 1100, 'move', 'multiple', 27);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1100, 'login', 'login', 28),
      (1, 1100, 'logout', 'logout', 29);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1101, 'apply', 'edit', 4),
      (1, 1101, 'cancel', 'edit', 5),
      (1, 1101, 'create', 'edit', 6),
      (1, 1101, 'save', 'edit', 7),
      (1, 1101, 'saveascopy', 'edit', 8),
      (1, 1101, 'saveandnew', 'edit', 9),
      (1, 1101, 'restore', 'edit', 10);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1101, 'archive', 'multiple', 11),
      (1, 1101, 'publish', 'multiple', 12),
      (1, 1101, 'unpublish', 'multiple', 13),
      (1, 1101, 'spam', 'multiple', 14),
      (1, 1101, 'trash', 'multiple', 15),
      (1, 1101, 'feature', 'multiple', 16),
      (1, 1101, 'unfeature', 'multiple', 17),
      (1, 1101, 'sticky', 'multiple', 18),
      (1, 1101, 'unsticky', 'multiple', 19),
      (1, 1101, 'checkin', 'multiple', 20),
      (1, 1101, 'reorder', 'multiple', 21),
      (1, 1101, 'orderup', 'multiple', 22),
      (1, 1101, 'orderdown', 'multiple', 23),
      (1, 1101, 'saveorder', 'multiple', 24),
      (1, 1101, 'delete', 'multiple', 25),
      (1, 1101, 'copy', 'multiple', 26),
      (1, 1101, 'move', 'multiple', 27);

INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1101, 'login', 'login', 28),
      (1, 1101, 'logout', 'login', 29);

# OPTION

# 1800 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_OPTION
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 1800, '', '', 0),
      (1, 1800, 2552, 2552, 1),
      (1, 1801, '', '', 0),
      (1, 1801, 2559, 2559, 1);

# VIEWS

# 2000 MOLAJO_EXTENSION_OPTION_ID_VIEWS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 2000, '', '', 0),
      (1, 2000, 'display', 'display', 1),
      (1, 2000, 'edit', 'edit', 2);

# 2100 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_VIEW
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 2100, '', '', 0),
      (1, 2100, 'display', 'display', 1);

# VIEW LAYOUTS

# 3000 MOLAJO_EXTENSION_OPTION_ID_DISPLAY_VIEW_LAYOUTS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 3000, '', '', 0),
      (1, 3000, 'default', 'default', 1),
      (1, 3000, 'item', 'item', 1),
      (1, 3000, 'items', 'items', 1),
      (1, 3000, 'table', 'table', 1);

# 3100 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 3100, '', '', 0),
      (1, 3100, 'default', 'default', 1);

# 3200 MOLAJO_EXTENSION_OPTION_ID_EDIT_VIEW_LAYOUTS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 3200, '', '', 0),
      (1, 3200, 'default', 'default', 1);

# 3300 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 3300, '', '', 0),
      (1, 3300, 'default', 'default', 1);

# VIEW FORMATS

# 4000 MOLAJO_EXTENSION_OPTION_ID_DISPLAY_VIEW_FORMATS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 4000, '', '', 0),
      (1, 4000, 'html', 'html', 1);

# 4100 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 4100, '', '', 0),
      (1, 4100, 'html', 'html', 1);

# 4200 MOLAJO_EXTENSION_OPTION_ID_EDIT_VIEW_FORMATS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 4200, '', '', 0),
      (1, 4200, 'error', 'error', 1),
      (1, 4200, 'feed', 'feed', 2),
      (1, 4200, 'html', 'html', 3),
      (1, 4200, 'json', 'json', 4),
      (1, 4200, 'opensearch', 'opensearch', 5),
      (1, 4200, 'raw', 'raw', 6),
      (1, 4200, 'xls', 'xls', 7),
      (1, 4200, 'xml', 'xml', 8),
      (1, 4200, 'xmlrpc', 'xmlrpc', 9);

# 4300 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 4300, '', '', 0),
      (1, 4300, 'html', 'html', 1);

# 6000 MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 6000, '', '', 0),
      (1, 6000, 'content', 'content', 1);

/** ACL Component Information

/** 10000 MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 10000, '', '', 0),
      (1, 10000, 1, 'Core ACL Implementation', 1);

/** 10100 MOLAJO_EXTENSION_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 10100, '', '', 0),
      (1, 10100, 'view', 'view', 1),
      (1, 10100, 'create', 'create', 2),
      (1, 10100, 'edit', 'edit', 3),
      (1, 10100, 'publish', 'publish', 4),
      (1, 10100, 'delete', 'delete', 5),
      (1, 10100, 'admin', 'admin', 6);

/** 10000 MOLAJO_EXTENSION_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 10200, '', '', 0),
      (1, 10200, 'add', 'create', 1),
      (1, 10200, 'admin', 'admin', 2),
      (1, 10200, 'apply', 'edit', 3),
      (1, 10200, 'archive', 'publish', 4),
      (1, 10200, 'cancel', '', 5),
      (1, 10200, 'checkin', 'admin', 6),
      (1, 10200, 'close', '', 7),
      (1, 10200, 'copy', 'create', 8),
      (1, 10200, 'create', 'create', 9),
      (1, 10200, 'delete', 'delete', 10),
      (1, 10200, 'view', 'view', 11),
      (1, 10200, 'edit', 'edit', 12),
      (1, 10200, 'editstate', 'publish', 13),
      (1, 10200, 'feature', 'publish', 14),
      (1, 10200, 'login', 'login', 15),
      (1, 10200, 'logout', 'logout', 16),
      (1, 10200, 'manage', 'edit', 17),
      (1, 10200, 'move', 'edit', 18),
      (1, 10200, 'orderdown', 'publish', 19),
      (1, 10200, 'orderup', 'publish', 20),
      (1, 10200, 'publish', 'publish', 21),
      (1, 10200, 'reorder', 'publish', 22),
      (1, 10200, 'restore', 'publish', 23),
      (1, 10200, 'save', 'edit', 24),
      (1, 10200, 'saveascopy', 'edit', 25),
      (1, 10200, 'saveandnew', 'edit', 26),
      (1, 10200, 'saveorder', 'publish', 27),
      (1, 10200, 'search', 'view', 28),
      (1, 10200, 'spam', 'publish', 29),
      (1, 10200, 'state', 'publish', 30),
      (1, 10200, 'sticky', 'publish', 31),
      (1, 10200, 'trash', 'publish', 32),
      (1, 10200, 'unfeature', 'publish', 33),
      (1, 10200, 'unpublish', 'publish', 34),
      (1, 10200, 'unsticky', 'publish', 35);

#
# com_login
#

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 100, '', '', 0),
(13, 100, '__dummy', '__dummy', 1);

/** MVC

# CONTROLLER TASKS

# 1100 MOLAJO_EXTENSION_OPTION_ID_TASK_TO_CONTROLLER
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 1100, '', '', 0),
(13, 1100, 'display', 'display', 3);

INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 1100, 'login', 'login', 28),
(13, 1100, 'logout', 'login', 29);

# VIEWS

# 2000 MOLAJO_EXTENSION_OPTION_ID_VIEWS
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 2000, '', '', 0),
(13, 2000, 'display', 'display', 1);

# 2100 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_VIEW
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 2100, '', '', 0),
(13, 2100, 'display', 'display', 1);

# VIEW LAYOUTS

# 3000 MOLAJO_EXTENSION_OPTION_ID_DISPLAY_VIEW_LAYOUTS
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 3000, '', '', 0),
(13, 3000, 'login', 'login', 1);

# 3100 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 3100, '', '', 0),
(13, 3100, 'login', 'login', 1);

# VIEW FORMATS

# 4000 MOLAJO_EXTENSION_OPTION_ID_DISPLAY_VIEW_FORMATS
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 4000, '', '', 0),
(13, 4000, 'html', 'html', 1),
(13, 4001, 'html', 'html', 1);

# MODELS

# 6000 MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 6000, '', '', 0),
(13, 6000, 'user', 'user', 1);

/** ACL Component Information

/** 10000 MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 10000, '', '', 0),
(13, 10000, 1, 'Core ACL Implementation', 1);

/** 10100 MOLAJO_EXTENSION_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 10100, '', '', 0),
(13, 10100, 'view', 'view', 1);

/** 10000 MOLAJO_EXTENSION_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 10200, '', '', 0),
(13, 10200, 'login', 'login', 15),
(13, 10200, 'logout', 'logout', 16);




/** com_dashboard

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(8, 100, '', '', 0),
(8, 100, '__dummy', '__dummy', 1);

# 3100 MOLAJO_EXTENSION_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
(8, 3100, '', '', 0),
(8, 3100, 'admin_dashboard', 'admin_dashboard', 1);



# com_articles

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
  VALUES
    (2, 100, '', '', 0),
    (2, 100, '__content', '__content', 1);

# Parameters
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'parameter_template_id', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_ID', 50),
      (1, 200, 'parameter_template_layout', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_LAYOUT', 50),
      (1, 200, 'parameter_show_author', 'MOLAJO_FIELD_PARAMETERS_SHOW_AUTHOR', 50),
      (1, 200, 'parameter_author_url', 'MOLAJO_FIELD_PARAMETERS_AUTHOR_URL', 51),
      (1, 200, 'parameter_author_profile_module', 'MOLAJO_FIELD_PARAMETERS_AUTHOR_PROFILE_MODULE', 52);

# Custom Fields
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'custom_fields_image1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_LABEL', 34),
      (1, 200, 'custom_fields_image1_file', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_FILE', 34),
      (1, 200, 'custom_fields_image1_credit', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_CREDIT', 34),
      (1, 200, 'custom_fields_image2_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE2_LABEL', 34),
      (1, 200, 'custom_fields_image2_file', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE2_FILE', 34),
      (1, 200, 'custom_fields_image2_credit', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE2_CREDIT', 34),
      (1, 200, 'custom_fields_image3_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE3_LABEL', 34),
      (1, 200, 'custom_fields_image3_file', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE3_FILE', 34),
      (1, 200, 'custom_fields_image3_credit', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE3_CREDIT', 34),

      (1, 200, 'custom_fields_link1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK1_LABEL', 34),
      (1, 200, 'custom_fields_link1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK1_FILE', 34),
      (1, 200, 'custom_fields_link2_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK2_LABEL', 34),
      (1, 200, 'custom_fields_link2_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK2_FILE', 34),
      (1, 200, 'custom_fields_link3_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK3_LABEL', 34),
      (1, 200, 'custom_fields_link3_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK3_FILE', 34),

      (1, 200, 'custom_fields_video1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_VIDEO1_LABEL', 34),
      (1, 200, 'custom_fields_video1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_VIDEO1_URL', 34),

      (1, 200, 'custom_fields_audio1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_AUDIO1_LABEL', 34),
      (1, 200, 'custom_fields_audio1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_AUDIO1_URL', 34),

      (1, 200, 'custom_fields_file1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE1_LABEL', 34),
      (1, 200, 'custom_fields_file1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE1_URL', 34),
      (1, 200, 'custom_fields_file2_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE2_LABEL', 34),
      (1, 200, 'custom_fields_file2_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE2_URL', 34),
      (1, 200, 'custom_fields_file3_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE3_LABEL', 34),
      (1, 200, 'custom_fields_file3_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE3_URL', 34);

# Parameters
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `option_id`, `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (1, 200, 'parameter_template_id_item', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_ID_ITEM', 50),
      (1, 200, 'parameter_template_page_item', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_PAGE_ITEM', 50),
      (1, 200, 'parameter_layout_item', 'MOLAJO_FIELD_PARAMETERS_LAYOUT_ITEM', 50),      
      (1, 200, 'parameter_template_id_items', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_ID_ITEM', 50),
      (1, 200, 'parameter_template_page_items', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_PAGE_ITEM', 50),
      (1, 200, 'parameter_layout_items', 'MOLAJO_FIELD_PARAMETERS_LAYOUT_ITEMS', 50),           
      (1, 200, 'parameter_show_author', 'MOLAJO_FIELD_PARAMETERS_SHOW_AUTHOR', 50),
      (1, 200, 'parameter_author_url', 'MOLAJO_FIELD_PARAMETERS_AUTHOR_URL', 51),
      (1, 200, 'parameter_author_profile_module', 'MOLAJO_FIELD_PARAMETERS_AUTHOR_PROFILE_MODULE', 52);