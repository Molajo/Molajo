
# articles

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
  VALUES
    (2, 100, '', '', 0),
    (2, 100, '__dummy', '__dummy', 1);

# Custom Fields
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 200, 'custom_fields_image1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_LABEL', 34),
      (@id, 0, 200, 'custom_fields_image1_file', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_FILE', 34),
      (@id, 0, 200, 'custom_fields_image1_credit', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE1_CREDIT', 34),
      (@id, 0, 200, 'custom_fields_image2_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE2_LABEL', 34),
      (@id, 0, 200, 'custom_fields_image2_file', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE2_FILE', 34),
      (@id, 0, 200, 'custom_fields_image2_credit', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE2_CREDIT', 34),
      (@id, 0, 200, 'custom_fields_image3_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE3_LABEL', 34),
      (@id, 0, 200, 'custom_fields_image3_file', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE3_FILE', 34),
      (@id, 0, 200, 'custom_fields_image3_credit', 'MOLAJO_FIELD_CUSTOM_FIELDS_IMAGE3_CREDIT', 34),

      (@id, 0, 200, 'custom_fields_link1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK1_LABEL', 34),
      (@id, 0, 200, 'custom_fields_link1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK1_FILE', 34),
      (@id, 0, 200, 'custom_fields_link2_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK2_LABEL', 34),
      (@id, 0, 200, 'custom_fields_link2_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK2_FILE', 34),
      (@id, 0, 200, 'custom_fields_link3_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK3_LABEL', 34),
      (@id, 0, 200, 'custom_fields_link3_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_LINK3_FILE', 34),

      (@id, 0, 200, 'custom_fields_video1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_VIDEO1_LABEL', 34),
      (@id, 0, 200, 'custom_fields_video1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_VIDEO1_URL', 34),

      (@id, 0, 200, 'custom_fields_audio1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_AUDIO1_LABEL', 34),
      (@id, 0, 200, 'custom_fields_audio1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_AUDIO1_URL', 34),

      (@id, 0, 200, 'custom_fields_file1_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE1_LABEL', 34),
      (@id, 0, 200, 'custom_fields_file1_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE1_URL', 34),
      (@id, 0, 200, 'custom_fields_file2_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE2_LABEL', 34),
      (@id, 0, 200, 'custom_fields_file2_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE2_URL', 34),
      (@id, 0, 200, 'custom_fields_file3_label', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE3_LABEL', 34),
      (@id, 0, 200, 'custom_fields_file3_url', 'MOLAJO_FIELD_CUSTOM_FIELDS_FILE3_URL', 34);

# Parameters
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 200, 'parameter_template_id_item', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_ID_ITEM', 50),
      (@id, 0, 200, 'parameter_template_page_item', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_PAGE_ITEM', 50),
      (@id, 0, 200, 'parameter_layout_item', 'MOLAJO_FIELD_PARAMETERS_LAYOUT_ITEM', 50),
      (@id, 0, 200, 'parameter_template_id_items', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_ID_ITEM', 50),
      (@id, 0, 200, 'parameter_template_page_items', 'MOLAJO_FIELD_PARAMETERS_TEMPLATE_PAGE_ITEM', 50),
      (@id, 0, 200, 'parameter_layout_items', 'MOLAJO_FIELD_PARAMETERS_LAYOUT_ITEMS', 50),
      (@id, 0, 200, 'parameter_show_author', 'MOLAJO_FIELD_PARAMETERS_SHOW_AUTHOR', 50),
      (@id, 0, 200, 'parameter_author_url', 'MOLAJO_FIELD_PARAMETERS_AUTHOR_URL', 51),
      (@id, 0, 200, 'parameter_author_profile_module', 'MOLAJO_FIELD_PARAMETERS_AUTHOR_PROFILE_MODULE', 52);