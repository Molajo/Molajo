
# MVC

# CONTROLLER TASKS

# 1100 MOLAJO_EXTENSION_OPTION_ID_TASKS_CONTROLLER
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
    VALUES
      (13, 1100, '', '', 0),
      (13, 1100, 'display', 'display', 3);

INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 1100, 'login', 'login', 28),
(13, 1100, 'logout', 'login', 29);



#
# login
#

# 100 MOLAJO_EXTENSION_OPTION_ID_TABLE
INSERT INTO `molajo_extension_options`
  (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`)
  VALUES
    (13, 100, '', '', 0),
    (13, 100, '__dummy', '__dummy', 1);


# VIEWS

# 2000 MOLAJO_EXTENSION_OPTION_ID_VIEWS
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 2000, '', '', 0),
(13, 2000, 'display', 'display', 1);

# 2100 MOLAJO_EXTENSION_OPTION_ID_VIEWS_DEFAULT
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 2100, '', '', 0),
(13, 2100, 'display', 'display', 1);

# VIEW LAYOUTS

# 3000 MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_DISPLAY
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 3000, '', '', 0),
(13, 3000, 'login', 'login', 1);

# 3100 MOLAJO_EXTENSION_OPTION_ID_LAYOUTS_DISPLAY_DEFAULT
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 3100, '', '', 0),
(13, 3100, 'login', 'login', 1);

# VIEW FORMATS

# 4000 MOLAJO_EXTENSION_OPTION_ID_FORMATS
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 4000, '', '', 0),
(13, 4000, 'html', 'html', 1),
(13, 4001, 'html', 'html', 1);

# MODELS

# 6000 MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 6000, '', '', 0),
(13, 6000, 'user', 'user', 1);

/** ACL Component Information

/** 10000 MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 10000, '', '', 0),
(13, 10000, 1, 'Core ACL Implementation', 1);

/** 10100 MOLAJO_EXTENSION_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 10100, '', '', 0),
(13, 10100, 'view', 'view', 1);

/** 10000 MOLAJO_EXTENSION_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `molajo_extension_options` (`extension_instance_id`, `application_id`, `option_id`,  `option_value`, `option_value_literal`, `ordering`) VALUES
(13, 10200, '', '', 0),
(13, 10200, 'login', 'login', 15),
(13, 10200, 'logout', 'logout', 16);
