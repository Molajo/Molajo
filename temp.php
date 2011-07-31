
#
# Configuration
#

/* 001 MOLAJO_CONFIG_OPTION_ID_FIELDS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1, '', '', 0),
('core', 1, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 1, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 2),
('core', 1, 'asset_id', 'MOLAJO_FIELD_ASSET_ID_LABEL', 3),
('core', 1, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 4),
('core', 1, 'catid', 'MOLAJO_FIELD_CATID_LABEL', 5),
('core', 1, 'checked_out', 'MOLAJO_FIELD_CHECKED_OUT_LABEL', 6),
('core', 1, 'checked_out_time', 'MOLAJO_FIELD_CHECKED_OUT_TIME_LABEL', 7),
('core', 1, 'component_id', 'MOLAJO_FIELD_COMPONENT_ID_LABEL', 8),
('core', 1, 'content_table', 'MOLAJO_FIELD_content_table_LABEL', 9),
('core', 1, 'content_email_address', 'MOLAJO_FIELD_CONTENT_EMAIL_ADDRESS_LABEL', 10),
('core', 1, 'content_file', 'MOLAJO_FIELD_CONTENT_FILE_LABEL', 11),
('core', 1, 'content_link', 'MOLAJO_FIELD_CONTENT_LINK_LABEL', 12),
('core', 1, 'content_numeric_value', 'MOLAJO_FIELD_CONTENT_NUMERIC_VALUE_LABEL', 13),
('core', 1, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 14),
('core', 1, 'content_type', 'MOLAJO_FIELD_CONTENT_TYPE_LABEL', 15),
('core', 1, 'created', 'MOLAJO_FIELD_CREATED_LABEL', 16),
('core', 1, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 17),
('core', 1, 'created_by_alias', 'MOLAJO_FIELD_CREATED_BY_ALIAS_LABEL', 18),
('core', 1, 'created_by_email', 'MOLAJO_FIELD_CREATED_BY_EMAIL_LABEL', 19),
('core', 1, 'created_by_ip_address', 'MOLAJO_FIELD_CREATED_BY_IP_ADDRESS_LABEL', 20),
('core', 1, 'created_by_referer', 'MOLAJO_FIELD_CREATED_BY_REFERER_LABEL', 21),
('core', 1, 'created_by_website', 'MOLAJO_FIELD_CREATED_BY_WEBSITE_LABEL', 22),
('core', 1, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 23),
('core', 1, 'id', 'MOLAJO_FIELD_ID_LABEL', 24),
('core', 1, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 25),
('core', 1, 'level', 'MOLAJO_FIELD_LEVEL_LABEL', 26),
('core', 1, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 27),
('core', 1, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 28),
('core', 1, 'metadesc', 'MOLAJO_FIELD_METADESC_LABEL', 29),
('core', 1, 'metakey', 'MOLAJO_FIELD_METAKEY_LABEL', 30),
('core', 1, 'meta_author', 'MOLAJO_FIELD_META_AUTHOR_LABEL', 31),
('core', 1, 'meta_rights', 'MOLAJO_FIELD_META_RIGHTS_LABEL', 32),
('core', 1, 'meta_robots', 'MOLAJO_FIELD_META_ROBOTS_LABEL', 33),
('core', 1, 'modified', 'MOLAJO_FIELD_MODIFIED_LABEL', 34),
('core', 1, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 35),
('core', 1, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 36),
('core', 1, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 37),
('core', 1, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 38),
('core', 1, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 39),
('core', 1, 'state', 'MOLAJO_FIELD_STATE_LABEL', 40),
('core', 1, 'state_prior_to_version', 'MOLAJO_FIELD_STATE_PRIOR_TO_VERSION_LABEL', 41),
('core', 1, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 42),
('core', 1, 'user_default', 'MOLAJO_FIELD_user_default_LABEL', 43),
('core', 1, 'category_default', 'MOLAJO_FIELD_category_default_LABEL', 43),
('core', 1, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 43),
('core', 1, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 44),
('core', 1, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 45);

/* 002 MOLAJO_CONFIG_OPTION_ID_PUBLISH_FIELDS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2, '', '', 0),
('core', 2, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 2, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2),
('core', 2, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3),
('core', 2, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4),
('core', 2, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5),
('core', 2, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6),
('core', 2, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);

/* 003 MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3, '', '', 0),
('core', 3, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1),
('core', 3, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2),
('core', 3, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);

/* 010 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10, '', '', 0),
('core', 10, 'content_type', 'Content Type', 1);

/* VIEWS */

/* 020 MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 20, '', '', 0),
('core', 20, 'edit', 'display', 1);

/* TABLE */

/* 045 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 45, '', '', 0),
('core', 45, '__multiple', '__multiple', 1);

/* FORMAT */

/* 075 MOLAJO_CONFIG_OPTION_ID_FORMAT */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 75, '', '', 0),
('core', 75, 'feed', 'feed', 1),
('core', 75, 'html', 'html', 2),
('core', 75, 'json', 'json', 3),
('core', 75, 'opensearch', 'opensearch', 4),
('core', 75, 'raw', 'raw', 5),
('core', 75, 'xls', 'xls', 6),
('core', 75, 'xml', 'xml', 7),
('core', 75, 'xmlrpc', 'xmlrpc', 8);

/* TASKS */

/* 080 MOLAJO_CONFIG_OPTION_ID_DISPLAY_CONTROLLER_TASKS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 80, '', '', 0),
('core', 80, 'add', 'add', 1),
('core', 80, 'edit', 'edit', 2),
('core', 80, 'display', 'display', 3);

/** 085 MOLAJO_CONFIG_OPTION_ID_EDIT_CONTROLLER_TASKS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 85, '', '', 0),
('core', 85, 'apply', 'apply', 1),
('core', 85, 'cancel', 'cancel', 2),
('core', 85, 'create', 'create', 3),
('core', 85, 'save', 'save', 4),
('core', 85, 'save2copy', 'save2copy', 5),
('core', 85, 'save2new', 'save2new', 6),
('core', 85, 'restore', 'restore', 7);

/** 090 MOLAJO_CONFIG_OPTION_ID_MULTIPLE_CONTROLLER_TASKS **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 90, '', '', 0),
('core', 90, 'archive', 'archive', 1),
('core', 90, 'publish', 'publish', 2),
('core', 90, 'unpublish', 'unpublish', 3),
('core', 90, 'spam', 'spam', 4),
('core', 90, 'trash', 'trash', 5),
('core', 90, 'feature', 'feature', 6),
('core', 90, 'unfeature', 'unfeature', 7),
('core', 90, 'sticky', 'sticky', 8),
('core', 90, 'unsticky', 'unsticky', 9),
('core', 90, 'checkin', 'checkin', 10),
('core', 90, 'reorder', 'reorder', 11),
('core', 90, 'orderup', 'orderup', 12),
('core', 90, 'orderdown', 'orderdown', 13),
('core', 90, 'saveorder', 'saveorder', 14),
('core', 90, 'delete', 'delete', 15),
('core', 90, 'copy', 'copy', 16),
('core', 90, 'move', 'move', 17);

/** 100 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 100, '', '', 0),
('core', 100, 'add', 'create', 1),
('core', 100, 'admin', 'admin', 2),
('core', 100, 'apply', 'edit', 3),
('core', 100, 'archive', 'publish', 4),
('core', 100, 'cancel', '', 5),
('core', 100, 'checkin', 'admin', 6),
('core', 100, 'close', '', 7),
('core', 100, 'copy', 'create', 8),
('core', 100, 'create', 'create', 9),
('core', 100, 'delete', 'delete', 10),
('core', 100, 'view', 'view', 11),
('core', 100, 'edit', 'edit', 12),
('core', 100, 'publish', 'publish', 13),
('core', 100, 'feature', 'publish', 14),
('core', 100, 'manage', '', 15),
('core', 100, 'move', 'edit', 16),
('core', 100, 'orderdown', 'publish', 18),
('core', 100, 'orderup', 'publish', 19),
('core', 100, 'publish', 'publish', 20),
('core', 100, 'reorder', 'publish', 21),
('core', 100, 'restore', 'publish', 22),
('core', 100, 'save', 'edit', 23),
('core', 100, 'save2copy', 'edit', 24),
('core', 100, 'save2new', 'edit', 25),
('core', 100, 'saveorder', 'publish', 26),
('core', 100, 'search', 'view', 27),
('core', 100, 'spam', 'publish', 28),
('core', 100, 'state', 'publish', 29),
('core', 100, 'sticky', 'publish', 30),
('core', 100, 'trash', 'publish', 31),
('core', 100, 'unfeature', 'publish', 32),
('core', 100, 'unpublish', 'publish', 33),
('core', 100, 'unsticky', 'publish', 34);

/** 110 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 110, '', '', 0),
('core', 110, 'simple', 'Simple ACL Implementation', 1);

/** 120 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 120, '', '', 0),
('core', 120, 'create', 'create', 1),
('core', 120, 'view', 'view', 2),
('core', 120, 'edit', 'edit', 3),
('core', 120, 'publish', 'publish', 4),
('core', 120, 'trash', 'trash', 5),
('core', 120, 'delete', 'delete', 6),
('core', 120, 'restore', 'restore', 7);

/* 200 MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 200, '', '', 0),
('core', 200, 'archive', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_ARCHIVE', 1),
('core', 200, 'checkin', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CHECKIN', 2),
('core', 200, 'delete', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_DELETE', 3),
('core', 200, 'edit', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_EDIT', 4),
('core', 200, 'feature', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_FEATURE', 5),
('core', 200, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 6),
('core', 200, 'new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_NEW', 7),
('core', 200, 'options', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_OPTIONS', 8),
('core', 200, 'publish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_PUBLISH', 9),
('core', 200, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 10),
('core', 200, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 11),
('core', 200, 'spam', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SPAM', 12),
('core', 200, 'sticky', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_STICKY', 13),
('core', 200, 'trash', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_TRASH', 14),
('core', 200, 'unpublish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_UNPUBLISH', 15);

/* 210 MOLAJO_CONFIG_OPTION_ID_EDIT_TOOLBAR_BUTTONS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 210, '', '', 0),
('core', 210, 'apply', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_APPLY', 1),
('core', 210, 'close', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CLOSE', 2),
('core', 210, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 3),
('core', 210, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 4),
('core', 210, 'save', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE', 5),
('core', 210, 'save2new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AND_NEW', 6),
('core', 210, 'save2copy', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AS_COPY', 7),
('core', 210, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 8);

/* 220 MOLAJO_CONFIG_OPTION_ID_TOOLBAR_SUBMENU_LINKS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 220, '', '', 0),
('core', 220, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1),
('core', 220, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2),
('core', 220, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3),
('core', 220, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4),
('core', 220, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5),
('core', 220, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);

/* 230 MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 230, '', '', 0),
('core', 230, 'access', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ACCESS', 1),
('core', 230, 'alias', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ALIAS', 2),
('core', 230, 'created_by', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_AUTHOR', 3),
('core', 230, 'catid', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CATEGORY', 4),
('core', 230, 'content_type', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CONTENT_TYPE', 5),
('core', 230, 'created', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CREATE_DATE', 6),
('core', 230, 'featured', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_FEATURED', 7),
('core', 230, 'language', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_LANGUAGE', 9),
('core', 230, 'modified', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_UPDATE_DATE', 10),
('core', 230, 'publish_up', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_PUBLISH_DATE', 11),
('core', 230, 'state', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STATE', 12),
('core', 230, 'stickied', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STICKIED', 13),
('core', 230, 'title', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_TITLE', 14);

/* 240 MOLAJO_CONFIG_OPTION_ID_EDITOR_BUTTONS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 240, '', '', 0),
('core', 240, 'article', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_ARTICLE', 1),
('core', 240, 'audio', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_AUDIO', 2),
('core', 240, 'file', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_FILE', 3),
('core', 240, 'gallery', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_GALLERY', 4),
('core', 240, 'image', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_IMAGE', 5),
('core', 240, 'pagebreak', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_PAGEBREAK', 6),
('core', 240, 'readmore', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_READMORE', 7),
('core', 240, 'video', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_VIDEO', 8);

/* 250 MOLAJO_CONFIG_OPTION_ID_STATE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 250, '', '', 0),
('core', 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1),
('core', 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2),
('core', 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3),
('core', 250, '-1', 'MOLAJO_OPTION_TRASHED', 4),
('core', 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5),
('core', 250, '-10', 'MOLAJO_OPTION_VERSION', 6);

/* 500 MOLAJO_CONFIG_OPTION_ID_DISPLAY_LAYOUTS_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 500, '', '', 0),
('core', 500, 'article', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_ARTICLE', 1),
('core', 500, 'banner', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_BANNER', 2),
('core', 500, 'contact', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_CONTACT', 3),
('core', 500, 'contact_form', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_CONTACT_FORM', 4),
('core', 500, 'media', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_MEDIA', 5),
('core', 500, 'newsfeed', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_NEWSFEED', 6),
('core', 500, 'item', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_ITEM', 7),
('core', 500, 'user', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_USER', 8),
('core', 500, 'weblink', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_WEBLINK', 9),
('core', 500, 'category', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_CATEGORY', 10),
('core', 500, 'blog', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_BLOG', 11),
('core', 500, 'integration', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_INTEGRATION', 12),
('core', 500, 'list', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_LIST', 13),
('core', 500, 'manager', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_MANAGER', 14);

/* MIME from ftp://ftp.iana.org/assignments/media-types/ */

/* 1000 MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1000, '', '', 0),
('core', 1000, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1),
('core', 1000, 'sp-midi', 'sp-midi', 2),
('core', 1000, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3),
('core', 1000, 'vnd.4SB', 'vnd.4SB', 4),
('core', 1000, 'vnd.CELP', 'vnd.CELP', 5),
('core', 1000, 'vnd.audiokoz', 'vnd.audiokoz', 6),
('core', 1000, 'vnd.cisco.nse', 'vnd.cisco.nse', 7),
('core', 1000, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8),
('core', 1000, 'vnd.cns.anp1', 'vnd.cns.anp1', 9),
('core', 1000, 'vnd.cns.inf1', 'vnd.cns.inf1', 10),
('core', 1000, 'vnd.dece.audio', 'vnd.dece.audio', 11),
('core', 1000, 'vnd.digital-winds', 'vnd.digital-winds', 12),
('core', 1000, 'vnd.dlna.adts', 'vnd.dlna.adts', 13),
('core', 1000, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14),
('core', 1000, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15),
('core', 1000, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16),
('core', 1000, 'vnd.dolby.mps', 'vnd.dolby.mps', 17),
('core', 1000, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18),
('core', 1000, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19),
('core', 1000, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20),
('core', 1000, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21),
('core', 1000, 'vnd.dra', 'vnd.dra', 22),
('core', 1000, 'vnd.dts', 'vnd.dts', 23),
('core', 1000, 'vnd.dts.hd', 'vnd.dts.hd', 24),
('core', 1000, 'vnd.dvb.file', 'vnd.dvb.file', 25),
('core', 1000, 'vnd.everad.plj', 'vnd.everad.plj', 26),
('core', 1000, 'vnd.hns.audio', 'vnd.hns.audio', 27),
('core', 1000, 'vnd.lucent.voice', 'vnd.lucent.voice', 28),
('core', 1000, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29),
('core', 1000, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30),
('core', 1000, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31),
('core', 1000, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32),
('core', 1000, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33),
('core', 1000, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34),
('core', 1000, 'vnd.octel.sbc', 'vnd.octel.sbc', 35),
('core', 1000, 'vnd.qcelp', 'vnd.qcelp', 36),
('core', 1000, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37),
('core', 1000, 'vnd.rip', 'vnd.rip', 38),
('core', 1000, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39),
('core', 1000, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);

/* 1010 MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1010, '', '', 0),
('core', 1010, 'cgm', 'cgm', 1),
('core', 1010, 'jp2', 'jp2', 2),
('core', 1010, 'jpm', 'jpm', 3),
('core', 1010, 'jpx', 'jpx', 4),
('core', 1010, 'naplps', 'naplps', 5),
('core', 1010, 'png', 'png', 6),
('core', 1010, 'prs.btif', 'prs.btif', 7),
('core', 1010, 'prs.pti', 'prs.pti', 8),
('core', 1010, 'vnd-djvu', 'vnd-djvu', 9),
('core', 1010, 'vnd-svf', 'vnd-svf', 10),
('core', 1010, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11),
('core', 1010, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12),
('core', 1010, 'vnd.cns.inf2', 'vnd.cns.inf2', 13),
('core', 1010, 'vnd.dece.graphic', 'vnd.dece.graphic', 14),
('core', 1010, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15),
('core', 1010, 'vnd.dwg', 'vnd.dwg', 16),
('core', 1010, 'vnd.dxf', 'vnd.dxf', 17),
('core', 1010, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18),
('core', 1010, 'vnd.fpx', 'vnd.fpx', 19),
('core', 1010, 'vnd.fst', 'vnd.fst', 20),
('core', 1010, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21),
('core', 1010, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22),
('core', 1010, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23),
('core', 1010, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24),
('core', 1010, 'vnd.mix', 'vnd.mix', 25),
('core', 1010, 'vnd.ms-modi', 'vnd.ms-modi', 26),
('core', 1010, 'vnd.net-fpx', 'vnd.net-fpx', 27),
('core', 1010, 'vnd.radiance', 'vnd.radiance', 28),
('core', 1010, 'vnd.sealed-png', 'vnd.sealed-png', 29),
('core', 1010, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30),
('core', 1010, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31),
('core', 1010, 'vnd.xiff', 'vnd.xiff', 32);

/* 1020 MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1020, '', '', 0),
('core', 1020, 'n3', 'n3', 1),
('core', 1020, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2),
('core', 1020, 'prs.lines.tag', 'prs.lines.tag', 3),
('core', 1020, 'rtf', 'rtf', 4),
('core', 1020, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5),
('core', 1020, 'tab-separated-values', 'tab-separated-values', 6),
('core', 1020, 'turtle', 'turtle', 7),
('core', 1020, 'vnd-curl', 'vnd-curl', 8),
('core', 1020, 'vnd.DMClientScript', 'vnd.DMClientScript', 9),
('core', 1020, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10),
('core', 1020, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11),
('core', 1020, 'vnd.abc', 'vnd.abc', 12),
('core', 1020, 'vnd.curl', 'vnd.curl', 13),
('core', 1020, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14),
('core', 1020, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15),
('core', 1020, 'vnd.fly', 'vnd.fly', 16),
('core', 1020, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17),
('core', 1020, 'vnd.graphviz', 'vnd.graphviz', 18),
('core', 1020, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19),
('core', 1020, 'vnd.in3d.spot', 'vnd.in3d.spot', 20),
('core', 1020, 'vnd.latex-z', 'vnd.latex-z', 21),
('core', 1020, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22),
('core', 1020, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23),
('core', 1020, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24),
('core', 1020, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25),
('core', 1020, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26),
('core', 1020, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27),
('core', 1020, 'vnd.wap-wml', 'vnd.wap-wml', 28),
('core', 1020, 'vnd.wap.si', 'vnd.wap.si', 29),
('core', 1020, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);

/* 1030 MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1030, '', '', 0),
('core', 1030, 'jpm', 'jpm', 1),
('core', 1030, 'mj2', 'mj2', 2),
('core', 1030, 'quicktime', 'quicktime', 3),
('core', 1030, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4),
('core', 1030, 'vnd-mpegurl', 'vnd-mpegurl', 5),
('core', 1030, 'vnd-vivo', 'vnd-vivo', 6),
('core', 1030, 'vnd.CCTV', 'vnd.CCTV', 7),
('core', 1030, 'vnd.dece-mp4', 'vnd.dece-mp4', 8),
('core', 1030, 'vnd.dece.hd', 'vnd.dece.hd', 9),
('core', 1030, 'vnd.dece.mobile', 'vnd.dece.mobile', 10),
('core', 1030, 'vnd.dece.pd', 'vnd.dece.pd', 11),
('core', 1030, 'vnd.dece.sd', 'vnd.dece.sd', 12),
('core', 1030, 'vnd.dece.video', 'vnd.dece.video', 13),
('core', 1030, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14),
('core', 1030, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15),
('core', 1030, 'vnd.dvb.file', 'vnd.dvb.file', 16),
('core', 1030, 'vnd.fvt', 'vnd.fvt', 17),
('core', 1030, 'vnd.hns.video', 'vnd.hns.video', 18),
('core', 1030, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19),
('core', 1030, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20),
('core', 1030, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21),
('core', 1030, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22),
('core', 1030, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23),
('core', 1030, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24),
('core', 1030, 'vnd.motorola.video', 'vnd.motorola.video', 25),
('core', 1030, 'vnd.motorola.videop', 'vnd.motorola.videop', 26),
('core', 1030, 'vnd.mpegurl', 'vnd.mpegurl', 27),
('core', 1030, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28),
('core', 1030, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29),
('core', 1030, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30),
('core', 1030, 'vnd.objectvideo', 'vnd.objectvideo', 31),
('core', 1030, 'vnd.sealed-swf', 'vnd.sealed-swf', 32),
('core', 1030, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33),
('core', 1030, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34),
('core', 1030, 'vnd.sealed.swf', 'vnd.sealed.swf', 35),
('core', 1030, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36),
('core', 1030, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);

/* ARTICLE CONFIGURATION FIELDS */

/* 010 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 10, '', '', 0),
('com_articles', 10, 'articles', 'Articles', 1);

/* TABLE */

/* 045 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 45, '', '', 0),
('com_articles', 45, '__articles', '__articles', 1);

/* 050 MOLAJO_CONFIG_OPTION_ID_EDIT_LAYOUTS_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 50, '', '', 0),
('com_articles', 50, 'default', 'default', 1);
        
/* 051 MOLAJO_CONFIG_OPTION_ID_DEFAULT_LAYOUTS_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 51, '', '', 0),
('com_articles', 51, 'default', 'default', 1);
        
/* 052 MOLAJO_CONFIG_OPTION_ID_DISPLAY_LAYOUTS_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 52, '', '', 0),
('com_articles', 52, 'default', 'default', 1);

/* 055 MOLAJO_CONFIG_OPTION_ID_EDIT_LAYOUTS_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 55, '', '', 0),
('com_articles', 55, 'default', 'default', 1);
        
/* 056 MOLAJO_CONFIG_OPTION_ID_DEFAULT_LAYOUTS_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 56, '', '', 0),
('com_articles', 56, 'default', 'default', 1);
        
/* 057 MOLAJO_CONFIG_OPTION_ID_DISPLAY_LAYOUTS_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 57, '', '', 0),
('com_articles', 57, 'default', 'default', 1),
('com_articles', 57, 'item', 'item', 2),
('com_articles', 57, 'items', 'items', 3),
('com_articles', 57, 'table', 'table', 4);

/* 075 MOLAJO_CONFIG_OPTION_ID_FORMAT */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 75, '', '', 0),
('com_articles', 75, 'feed', 'feed', 1),
('com_articles', 75, 'html', 'html', 2),
('com_articles', 75, 'json', 'json', 3),
('com_articles', 75, 'raw', 'raw', 5),
('com_articles', 75, 'xml', 'xml', 7);
