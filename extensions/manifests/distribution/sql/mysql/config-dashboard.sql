#
# Dashboard
#
SET @id = (SELECT id FROM molajo_extension_instances WHERE title = 'dashboard' AND asset_type_id = 1050);
SELECT @id;

# DEFAULT 

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 100, '', '', 0),
      (@id, 0, 100, '__dummy', '__dummy', 1);

# Parameters
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 200, 'template', 'molajito', 50),
      (@id, 0, 200, 'page', 'default', 50),
      (@id, 0, 200, 'layout', 'dashboard', 50),
      (@id, 0, 200, 'wrap', 'div', 50);

# VIEWS

# 2000 MOLAJO_EXTENSION_OPTION_ID_VIEWS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 2000, '', '', 0),
      (@id, 0, 2000, 'display', 'display', 1);

# 2100 MOLAJO_EXTENSION_OPTION_ID_VIEWS_DEFAULT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 2100, '', '', 0),
      (@id, 0, 2100, 'display', 'display', 1);

# LAYOUTS

# 3000 MOLAJO_EXTENSION_OPTION_ID_VIEWS_DISPLAY
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 3000, '', '', 0),
      (@id, 0, 3000, 'dashboard', 'dashboard', 1);

# 3100 MOLAJO_EXTENSION_OPTION_ID_VIEWS_DISPLAY_DEFAULT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 3100, '', '', 0),
      (@id, 0, 3100, 'dashboard', 'dashboard', 1);

# 3150 MOLAJO_EXTENSION_OPTION_ID_VIEWS_DISPLAY_DEFAULT_WRAP
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 3150, '', '', 0),
      (@id, 0, 3150, 'div', 'div', 1);

# 3200 MOLAJO_EXTENSION_OPTION_ID_VIEWS_EDIT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 3200, '', '', 0);

# 3300 MOLAJO_EXTENSION_OPTION_ID_VIEWS_EDIT_DEFAULT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 3300, '', '', 0);

# 3350 MOLAJO_EXTENSION_OPTION_ID_VIEWS_EDIT_DEFAULT_WRAP
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 3350, '', '', 0);

# FORMATS

# 4000 MOLAJO_EXTENSION_OPTION_ID_FORMATS
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 4000, '', '', 0),
      (@id, 0, 4000, 'html', 'html', 1);

# 4100 MOLAJO_EXTENSION_OPTION_ID_FORMATS_DEFAULT
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 4100, '', '', 0),
      (@id, 0, 4100, 'html', 'html', 1);

# 6000 MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 6000, '', '', 0);
