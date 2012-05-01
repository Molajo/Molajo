#
# Dashboard
#
SET @id = (SELECT id FROM molajo_extension_instances WHERE title = 'Dashboard' AND catalog_type_id = 1050);
SELECT @id;

# DEFAULT

# 100 EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (@id, 0, 100, '', '', 0),
      (@id, 0, 100, 'static', 'static', 1);
