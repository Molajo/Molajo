#
# Configuration
#

/* TABLE */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 100, '', '', 0),
('core', 100, '__common', '__common', 1);

/* 200 MOLAJO_CONFIG_OPTION_ID_FIELDS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 200, '', '', 0),
('core', 200, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 200, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 2),
('core', 200, 'asset_id', 'MOLAJO_FIELD_ASSET_ID_LABEL', 3),
('core', 200, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 4),
('core', 200, 'catid', 'MOLAJO_FIELD_CATID_LABEL', 5),
('core', 200, 'checked_out', 'MOLAJO_FIELD_CHECKED_OUT_LABEL', 6),
('core', 200, 'checked_out_time', 'MOLAJO_FIELD_CHECKED_OUT_TIME_LABEL', 7),
('core', 200, 'component_id', 'MOLAJO_FIELD_COMPONENT_ID_LABEL', 8),
('core', 200, 'content_table', 'MOLAJO_FIELD_CONTENT_TABLE_LABEL', 9),
('core', 200, 'content_email_address', 'MOLAJO_FIELD_CONTENT_EMAIL_ADDRESS_LABEL', 10),
('core', 200, 'content_file', 'MOLAJO_FIELD_CONTENT_FILE_LABEL', 11),
('core', 200, 'content_link', 'MOLAJO_FIELD_CONTENT_LINK_LABEL', 12),
('core', 200, 'content_numeric_value', 'MOLAJO_FIELD_CONTENT_NUMERIC_VALUE_LABEL', 13),
('core', 200, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 14),
('core', 200, 'content_type', 'MOLAJO_FIELD_CONTENT_TYPE_LABEL', 15),
('core', 200, 'created', 'MOLAJO_FIELD_CREATED_LABEL', 16),
('core', 200, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 17),
('core', 200, 'created_by_alias', 'MOLAJO_FIELD_CREATED_BY_ALIAS_LABEL', 18),
('core', 200, 'created_by_email', 'MOLAJO_FIELD_CREATED_BY_EMAIL_LABEL', 19),
('core', 200, 'created_by_ip_address', 'MOLAJO_FIELD_CREATED_BY_IP_ADDRESS_LABEL', 20),
('core', 200, 'created_by_referer', 'MOLAJO_FIELD_CREATED_BY_REFERER_LABEL', 21),
('core', 200, 'created_by_website', 'MOLAJO_FIELD_CREATED_BY_WEBSITE_LABEL', 22),
('core', 200, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 23),
('core', 200, 'id', 'MOLAJO_FIELD_ID_LABEL', 24),
('core', 200, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 25),
('core', 200, 'level', 'MOLAJO_FIELD_LEVEL_LABEL', 26),
('core', 200, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 27),
('core', 200, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 28),
('core', 200, 'metadesc', 'MOLAJO_FIELD_METADESC_LABEL', 29),
('core', 200, 'metakey', 'MOLAJO_FIELD_METAKEY_LABEL', 30),
('core', 200, 'meta_author', 'MOLAJO_FIELD_META_AUTHOR_LABEL', 31),
('core', 200, 'meta_rights', 'MOLAJO_FIELD_META_RIGHTS_LABEL', 32),
('core', 200, 'meta_robots', 'MOLAJO_FIELD_META_ROBOTS_LABEL', 33),
('core', 200, 'modified', 'MOLAJO_FIELD_MODIFIED_LABEL', 34),
('core', 200, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 35),
('core', 200, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 36),
('core', 200, 'stop_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 37),
('core', 200, 'start_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 38),
('core', 200, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 39),
('core', 200, 'state', 'MOLAJO_FIELD_STATE_LABEL', 40),
('core', 200, 'state_prior_to_version', 'MOLAJO_FIELD_STATE_PRIOR_TO_VERSION_LABEL', 41),
('core', 200, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 42),
('core', 200, 'user_default', 'MOLAJO_FIELD_USER_DEFAULT_LABEL', 43),
('core', 200, 'category_default', 'MOLAJO_FIELD_CATEGORY_DEFAULT_LABEL', 44),
('core', 200, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 45),
('core', 200, 'subtitle', 'MOLAJO_FIELD_SUBTITLE_LABEL', 46),
('core', 200, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 47),
('core', 200, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 48);

/* 210 MOLAJO_CONFIG_OPTION_ID_PUBLISH_FIELDS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 210, '', '', 0),
('core', 210, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 210, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2),
('core', 210, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3),
('core', 210, 'stop_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4),
('core', 210, 'start_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5),
('core', 210, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6),
('core', 210, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);

/* 220 MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 220, '', '', 0),
('core', 220, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1),
('core', 220, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2),
('core', 220, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);

/* 230 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 230, '', '', 0),
('core', 230, 'content_type', 'Content Type', 1);

/* 250 MOLAJO_CONFIG_OPTION_ID_STATUS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 250, '', '', 0),
('core', 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1),
('core', 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2),
('core', 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3),
('core', 250, '-1', 'MOLAJO_OPTION_TRASHED', 4),
('core', 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5),
('core', 250, '-10', 'MOLAJO_OPTION_VERSION', 6);

/* USER INTERFACE */

/* 300 MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 300, '', '', 0),
('core', 300, 'archive', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_ARCHIVE', 1),
('core', 300, 'checkin', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CHECKIN', 2),
('core', 300, 'delete', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_DELETE', 3),
('core', 300, 'edit', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_EDIT', 4),
('core', 300, 'feature', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_FEATURE', 5),
('core', 300, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 6),
('core', 300, 'new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_NEW', 7),
('core', 300, 'options', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_OPTIONS', 8),
('core', 300, 'publish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_PUBLISH', 9),
('core', 300, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 10),
('core', 300, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 11),
('core', 300, 'spam', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SPAM', 12),
('core', 300, 'sticky', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_STICKY', 13),
('core', 300, 'trash', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_TRASH', 14),
('core', 300, 'unpublish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_UNPUBLISH', 15);

/* 310 MOLAJO_CONFIG_OPTION_ID_EDIT_TOOLBAR_BUTTONS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 310, '', '', 0),
('core', 310, 'apply', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_APPLY', 1),
('core', 310, 'close', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CLOSE', 2),
('core', 310, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 3),
('core', 310, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 4),
('core', 310, 'save', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE', 5),
('core', 310, 'save2new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AND_NEW', 6),
('core', 310, 'save2copy', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AS_COPY', 7),
('core', 310, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 8);

/* 320 MOLAJO_CONFIG_OPTION_ID_TOOLBAR_SUBMENU_LINKS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 320, '', '', 0),
('core', 320, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1),
('core', 320, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2),
('core', 320, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3),
('core', 320, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4),
('core', 320, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5),
('core', 320, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);

/* 330 MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 330, '', '', 0),
('core', 330, 'access', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ACCESS', 1),
('core', 330, 'alias', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ALIAS', 2),
('core', 330, 'created_by', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_AUTHOR', 3),
('core', 330, 'catid', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CATEGORY', 4),
('core', 330, 'content_type', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CONTENT_TYPE', 5),
('core', 330, 'created', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CREATE_DATE', 6),
('core', 330, 'featured', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_FEATURED', 7),
('core', 330, 'language', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_LANGUAGE', 9),
('core', 330, 'modified', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_UPDATE_DATE', 10),
('core', 330, 'start_publishing_datetime', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_PUBLISH_DATE', 11),
('core', 330, 'state', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STATE', 12),
('core', 330, 'stickied', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STICKIED', 13),
('core', 330, 'title', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_TITLE', 14),
('core', 330, 'subtitle', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_SUBTITLE', 15);

/* 340 MOLAJO_CONFIG_OPTION_ID_EDITOR_BUTTONS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 340, '', '', 0),
('core', 340, 'article', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_ARTICLE', 1),
('core', 340, 'audio', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_AUDIO', 2),
('core', 340, 'file', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_FILE', 3),
('core', 340, 'gallery', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_GALLERY', 4),
('core', 340, 'image', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_IMAGE', 5),
('core', 340, 'pagebreak', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_PAGEBREAK', 6),
('core', 340, 'readmore', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_READMORE', 7),
('core', 340, 'video', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_VIDEO', 8);

/* MIME from ftp://ftp.iana.org/assignments/media-types/ */

/* 400 MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 400, '', '', 0),
('core', 400, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1),
('core', 400, 'sp-midi', 'sp-midi', 2),
('core', 400, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3),
('core', 400, 'vnd.4SB', 'vnd.4SB', 4),
('core', 400, 'vnd.CELP', 'vnd.CELP', 5),
('core', 400, 'vnd.audiokoz', 'vnd.audiokoz', 6),
('core', 400, 'vnd.cisco.nse', 'vnd.cisco.nse', 7),
('core', 400, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8),
('core', 400, 'vnd.cns.anp1', 'vnd.cns.anp1', 9),
('core', 400, 'vnd.cns.inf1', 'vnd.cns.inf1', 10),
('core', 400, 'vnd.dece.audio', 'vnd.dece.audio', 11),
('core', 400, 'vnd.digital-winds', 'vnd.digital-winds', 12),
('core', 400, 'vnd.dlna.adts', 'vnd.dlna.adts', 13),
('core', 400, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14),
('core', 400, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15),
('core', 400, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16),
('core', 400, 'vnd.dolby.mps', 'vnd.dolby.mps', 17),
('core', 400, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18),
('core', 400, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19),
('core', 400, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20),
('core', 400, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21),
('core', 400, 'vnd.dra', 'vnd.dra', 22),
('core', 400, 'vnd.dts', 'vnd.dts', 23),
('core', 400, 'vnd.dts.hd', 'vnd.dts.hd', 24),
('core', 400, 'vnd.dvb.file', 'vnd.dvb.file', 25),
('core', 400, 'vnd.everad.plj', 'vnd.everad.plj', 26),
('core', 400, 'vnd.hns.audio', 'vnd.hns.audio', 27),
('core', 400, 'vnd.lucent.voice', 'vnd.lucent.voice', 28),
('core', 400, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29),
('core', 400, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30),
('core', 400, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31),
('core', 400, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32),
('core', 400, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33),
('core', 400, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34),
('core', 400, 'vnd.octel.sbc', 'vnd.octel.sbc', 35),
('core', 400, 'vnd.qcelp', 'vnd.qcelp', 36),
('core', 400, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37),
('core', 400, 'vnd.rip', 'vnd.rip', 38),
('core', 400, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39),
('core', 400, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);

/* 410 MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 410, '', '', 0),
('core', 410, 'cgm', 'cgm', 1),
('core', 410, 'jp2', 'jp2', 2),
('core', 410, 'jpm', 'jpm', 3),
('core', 410, 'jpx', 'jpx', 4),
('core', 410, 'naplps', 'naplps', 5),
('core', 410, 'png', 'png', 6),
('core', 410, 'prs.btif', 'prs.btif', 7),
('core', 410, 'prs.pti', 'prs.pti', 8),
('core', 410, 'vnd-djvu', 'vnd-djvu', 9),
('core', 410, 'vnd-svf', 'vnd-svf', 10),
('core', 410, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11),
('core', 410, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12),
('core', 410, 'vnd.cns.inf2', 'vnd.cns.inf2', 13),
('core', 410, 'vnd.dece.graphic', 'vnd.dece.graphic', 14),
('core', 410, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15),
('core', 410, 'vnd.dwg', 'vnd.dwg', 16),
('core', 410, 'vnd.dxf', 'vnd.dxf', 17),
('core', 410, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18),
('core', 410, 'vnd.fpx', 'vnd.fpx', 19),
('core', 410, 'vnd.fst', 'vnd.fst', 20),
('core', 410, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21),
('core', 410, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22),
('core', 410, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23),
('core', 410, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24),
('core', 410, 'vnd.mix', 'vnd.mix', 25),
('core', 410, 'vnd.ms-modi', 'vnd.ms-modi', 26),
('core', 410, 'vnd.net-fpx', 'vnd.net-fpx', 27),
('core', 410, 'vnd.radiance', 'vnd.radiance', 28),
('core', 410, 'vnd.sealed-png', 'vnd.sealed-png', 29),
('core', 410, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30),
('core', 410, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31),
('core', 410, 'vnd.xiff', 'vnd.xiff', 32);

/* 420 MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 420, '', '', 0),
('core', 420, 'n3', 'n3', 1),
('core', 420, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2),
('core', 420, 'prs.lines.tag', 'prs.lines.tag', 3),
('core', 420, 'rtf', 'rtf', 4),
('core', 420, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5),
('core', 420, 'tab-separated-values', 'tab-separated-values', 6),
('core', 420, 'turtle', 'turtle', 7),
('core', 420, 'vnd-curl', 'vnd-curl', 8),
('core', 420, 'vnd.DMClientScript', 'vnd.DMClientScript', 9),
('core', 420, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10),
('core', 420, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11),
('core', 420, 'vnd.abc', 'vnd.abc', 12),
('core', 420, 'vnd.curl', 'vnd.curl', 13),
('core', 420, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14),
('core', 420, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15),
('core', 420, 'vnd.fly', 'vnd.fly', 16),
('core', 420, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17),
('core', 420, 'vnd.graphviz', 'vnd.graphviz', 18),
('core', 420, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19),
('core', 420, 'vnd.in3d.spot', 'vnd.in3d.spot', 20),
('core', 420, 'vnd.latex-z', 'vnd.latex-z', 21),
('core', 420, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22),
('core', 420, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23),
('core', 420, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24),
('core', 420, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25),
('core', 420, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26),
('core', 420, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27),
('core', 420, 'vnd.wap-wml', 'vnd.wap-wml', 28),
('core', 420, 'vnd.wap.si', 'vnd.wap.si', 29),
('core', 420, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);

/* 430 MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 430, '', '', 0),
('core', 430, 'jpm', 'jpm', 1),
('core', 430, 'mj2', 'mj2', 2),
('core', 430, 'quicktime', 'quicktime', 3),
('core', 430, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4),
('core', 430, 'vnd-mpegurl', 'vnd-mpegurl', 5),
('core', 430, 'vnd-vivo', 'vnd-vivo', 6),
('core', 430, 'vnd.CCTV', 'vnd.CCTV', 7),
('core', 430, 'vnd.dece-mp4', 'vnd.dece-mp4', 8),
('core', 430, 'vnd.dece.hd', 'vnd.dece.hd', 9),
('core', 430, 'vnd.dece.mobile', 'vnd.dece.mobile', 10),
('core', 430, 'vnd.dece.pd', 'vnd.dece.pd', 11),
('core', 430, 'vnd.dece.sd', 'vnd.dece.sd', 12),
('core', 430, 'vnd.dece.video', 'vnd.dece.video', 13),
('core', 430, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14),
('core', 430, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15),
('core', 430, 'vnd.dvb.file', 'vnd.dvb.file', 16),
('core', 430, 'vnd.fvt', 'vnd.fvt', 17),
('core', 430, 'vnd.hns.video', 'vnd.hns.video', 18),
('core', 430, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19),
('core', 430, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20),
('core', 430, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21),
('core', 430, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22),
('core', 430, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23),
('core', 430, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24),
('core', 430, 'vnd.motorola.video', 'vnd.motorola.video', 25),
('core', 430, 'vnd.motorola.videop', 'vnd.motorola.videop', 26),
('core', 430, 'vnd.mpegurl', 'vnd.mpegurl', 27),
('core', 430, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28),
('core', 430, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29),
('core', 430, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30),
('core', 430, 'vnd.objectvideo', 'vnd.objectvideo', 31),
('core', 430, 'vnd.sealed-swf', 'vnd.sealed-swf', 32),
('core', 430, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33),
('core', 430, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34),
('core', 430, 'vnd.sealed.swf', 'vnd.sealed.swf', 35),
('core', 430, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36),
('core', 430, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);

/** MVC */

/* CONTROLLER TASKS */

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, '', '', 0),
('core', 1100, 'add', 'display', 1),
('core', 1100, 'edit', 'display', 2),
('core', 1100, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'apply', 'edit', 4),
('core', 1100, 'cancel', 'edit', 5),
('core', 1100, 'create', 'edit', 6),
('core', 1100, 'save', 'edit', 7),
('core', 1100, 'save2copy', 'edit', 8),
('core', 1100, 'save2new', 'edit', 9),
('core', 1100, 'restore', 'edit', 10);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'archive', 'multiple', 11),
('core', 1100, 'publish', 'multiple', 12),
('core', 1100, 'unpublish', 'multiple', 13),
('core', 1100, 'spam', 'multiple', 14),
('core', 1100, 'trash', 'multiple', 15),
('core', 1100, 'feature', 'multiple', 16),
('core', 1100, 'unfeature', 'multiple', 17),
('core', 1100, 'sticky', 'multiple', 18),
('core', 1100, 'unsticky', 'multiple', 19),
('core', 1100, 'checkin', 'multiple', 20),
('core', 1100, 'reorder', 'multiple', 21),
('core', 1100, 'orderup', 'multiple', 22),
('core', 1100, 'orderdown', 'multiple', 23),
('core', 1100, 'saveorder', 'multiple', 24),
('core', 1100, 'delete', 'multiple', 25),
('core', 1100, 'copy', 'multiple', 26),
('core', 1100, 'move', 'multiple', 27);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'login', 'login', 28),
('core', 1100, 'logout', 'logout', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, '', '', 0),
('core', 1101, 'add', 'display', 1),
('core', 1101, 'edit', 'display', 2),
('core', 1101, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'apply', 'edit', 4),
('core', 1101, 'cancel', 'edit', 5),
('core', 1101, 'create', 'edit', 6),
('core', 1101, 'save', 'edit', 7),
('core', 1101, 'save2copy', 'edit', 8),
('core', 1101, 'save2new', 'edit', 9),
('core', 1101, 'restore', 'edit', 10);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'archive', 'multiple', 11),
('core', 1101, 'publish', 'multiple', 12),
('core', 1101, 'unpublish', 'multiple', 13),
('core', 1101, 'spam', 'multiple', 14),
('core', 1101, 'trash', 'multiple', 15),
('core', 1101, 'feature', 'multiple', 16),
('core', 1101, 'unfeature', 'multiple', 17),
('core', 1101, 'sticky', 'multiple', 18),
('core', 1101, 'unsticky', 'multiple', 19),
('core', 1101, 'checkin', 'multiple', 20),
('core', 1101, 'reorder', 'multiple', 21),
('core', 1101, 'orderup', 'multiple', 22),
('core', 1101, 'orderdown', 'multiple', 23),
('core', 1101, 'saveorder', 'multiple', 24),
('core', 1101, 'delete', 'multiple', 25),
('core', 1101, 'copy', 'multiple', 26),
('core', 1101, 'move', 'multiple', 27);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'login', 'login', 28),
('core', 1101, 'logout', 'login', 29);

/* OPTION */

/* 1800 MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1800, '', '', 0),
('core', 1800, 'com_articles', 'com_articles', 1),
('core', 1801, '', '', 0),
('core', 1801, 'com_login', 'com_login', 1);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2000, '', '', 0),
('core', 2000, 'display', 'display', 1),
('core', 2000, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2100, '', '', 0),
('core', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2001, '', '', 0),
('core', 2001, 'display', 'display', 1),
('core', 2001, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2101, '', '', 0),
('core', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3000, '', '', 0),
('core', 3000, 'default', 'default', 1),
('core', 3000, 'item', 'item', 1),
('core', 3000, 'items', 'items', 1),
('core', 3000, 'table', 'table', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3100, '', '', 0),
('core', 3100, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3200, '', '', 0),
('core', 3200, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3300, '', '', 0),
('core', 3300, 'default', 'default', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3001, '', '', 0),
('core', 3001, 'default', 'default', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3101, '', '', 0),
('core', 3101, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3201, '', '', 0),
('core', 3201, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3301, '', '', 0),
('core', 3301, 'default', 'default', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4000, '', '', 0),
('core', 4000, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4100, '', '', 0),
('core', 4100, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4200, '', '', 0),
('core', 4200, 'error', 'error', 1),
('core', 4200, 'feed', 'feed', 2),
('core', 4200, 'html', 'html', 3),
('core', 4200, 'json', 'json', 4),
('core', 4200, 'opensearch', 'opensearch', 5),
('core', 4200, 'raw', 'raw', 6),
('core', 4200, 'xls', 'xls', 7),
('core', 4200, 'xml', 'xml', 8),
('core', 4200, 'xmlrpc', 'xmlrpc', 9);

/* 4300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4300, '', '', 0),
('core', 4300, 'html', 'html', 1);


/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS +application id */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4001, '', '', 0),
('core', 4001, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4101, '', '', 0),
('core', 4101, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS +application id */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4201, '', '', 0),
('core', 4201, 'error', 'error', 1),
('core', 4201, 'feed', 'feed', 2),
('core', 4201, 'html', 'html', 3),
('core', 4201, 'json', 'json', 4),
('core', 4201, 'opensearch', 'opensearch', 5),
('core', 4201, 'raw', 'raw', 6),
('core', 4201, 'xls', 'xls', 7),
('core', 4201, 'xml', 'xml', 8),
('core', 4201, 'xmlrpc', 'xmlrpc', 9);

/* 4300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4301, '', '', 0),
('core', 4301, 'html', 'html', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5000, '', '', 0),
('core', 5000, 'display', 'display', 1),
('core', 5000, 'edit', 'edit', 2);

/* 5001 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5001, '', '', 0),
('core', 5001, 'display', 'display', 1),
('core', 5001, 'edit', 'edit', 2);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 6000, '', '', 0),
('core', 6000, 'content', 'content', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10000, '', '', 0),
('core', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10100, '', '', 0),
('core', 10100, 'view', 'view', 1),
('core', 10100, 'create', 'create', 2),
('core', 10100, 'edit', 'edit', 3),
('core', 10100, 'publish', 'publish', 4),
('core', 10100, 'delete', 'delete', 5),
('core', 10100, 'admin', 'admin', 6);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10200, '', '', 0),
('core', 10200, 'add', 'create', 1),
('core', 10200, 'admin', 'admin', 2),
('core', 10200, 'apply', 'edit', 3),
('core', 10200, 'archive', 'publish', 4),
('core', 10200, 'cancel', '', 5),
('core', 10200, 'checkin', 'admin', 6),
('core', 10200, 'close', '', 7),
('core', 10200, 'copy', 'create', 8),
('core', 10200, 'create', 'create', 9),
('core', 10200, 'delete', 'delete', 10),
('core', 10200, 'view', 'view', 11),
('core', 10200, 'edit', 'edit', 12),
('core', 10200, 'editstate', 'publish', 13),
('core', 10200, 'feature', 'publish', 14),
('core', 10200, 'login', 'login', 15),
('core', 10200, 'logout', 'logout', 16),
('core', 10200, 'manage', 'edit', 17),
('core', 10200, 'move', 'edit', 18),
('core', 10200, 'orderdown', 'publish', 19),
('core', 10200, 'orderup', 'publish', 20),
('core', 10200, 'publish', 'publish', 21),
('core', 10200, 'reorder', 'publish', 22),
('core', 10200, 'restore', 'publish', 23),
('core', 10200, 'save', 'edit', 24),
('core', 10200, 'save2copy', 'edit', 25),
('core', 10200, 'save2new', 'edit', 26),
('core', 10200, 'saveorder', 'publish', 27),
('core', 10200, 'search', 'view', 28),
('core', 10200, 'spam', 'publish', 29),
('core', 10200, 'state', 'publish', 30),
('core', 10200, 'sticky', 'publish', 31),
('core', 10200, 'trash', 'publish', 32),
('core', 10200, 'unfeature', 'publish', 33),
('core', 10200, 'unpublish', 'publish', 34),
('core', 10200, 'unsticky', 'publish', 35);

#
# com_login
#

/* TABLE */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 100, '', '', 0),
('com_login', 100, '__dummy', '__dummy', 1);

/** MVC */

/* CONTROLLER TASKS */

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, '', '', 0),
('com_login', 1100, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, 'login', 'login', 28),
('com_login', 1100, 'logout', 'login', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, '', '', 0),
('com_login', 1101, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, 'login', 'login', 28),
('com_login', 1101, 'logout', 'login', 29);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2000, '', '', 0),
('com_login', 2000, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2100, '', '', 0),
('com_login', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2001, '', '', 0),
('com_login', 2001, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2101, '', '', 0),
('com_login', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3000, '', '', 0),
('com_login', 3000, 'login', 'login', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3100, '', '', 0),
('com_login', 3100, 'login', 'login', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3001, '', '', 0),
('com_login', 3001, 'admin_login', 'admin_login', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3101, '', '', 0),
('com_login', 3101, 'admin_login', 'admin_login', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 4000, '', '', 0),
('com_login', 4000, 'html', 'html', 1),
('com_login', 4001, 'html', 'html', 1);

/* MODELS */

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5000, '', '', 0),
('com_login', 5000, 'dummy', 'dummy', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5001, '', '', 0),
('com_login', 5001, 'dummy', 'dummy', 1);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 6000, '', '', 0),
('com_login', 6000, 'user', 'user', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10000, '', '', 0),
('com_login', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10100, '', '', 0),
('com_login', 10100, 'view', 'view', 1);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10200, '', '', 0),
('com_login', 10200, 'login', 'login', 15),
('com_login', 10200, 'logout', 'logout', 16);

/* ARTICLES */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 100, '', '', 0),
('com_articles', 100, '__articles', '__articles', 1);