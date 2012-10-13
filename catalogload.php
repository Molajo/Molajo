/** POPULATE CATALOG TABLE FOR SOURCE */

/** 1. System Model: List  */

INSERT INTO `molajo_catalog`

	(`id`, `application_id`, `catalog_type_id`, `source_id`,
	`enabled`, `extension_instance_id`, `routable`, `menuitem_type`,
	`sef_request`, `redirect_to_id`, `view_group_id`,
	`primary_category_id`, `tinyurl`)

	SELECT DISTINCT NULL, c.id, a.catalog_type_id, `b`.`extension_instance_id`,
		0, 0, `b`.`routable`, 'List' AS `menuitem_type`,
		CONCAT( `b`.`alias` ), 0 AS `redirect_to_id`, 1 AS `view_group_id`,
		`b`.`primary_category_id` , '' AS `tinyurl`

		FROM `molajo_extension_instances` a,
			molajo_catalog_types b,
			molajo_applications c

		WHERE a.catalog_type_id =100000
			AND a.id = b.extension_instance_id

/** 2. System Model: Items  */

INSERT INTO `molajo_catalog`

	(`id`, `application_id`, `catalog_type_id`, `source_id`,
	`enabled`, `extension_instance_id`, `routable`, `menuitem_type`,
	`sef_request`, `redirect_to_id`, `view_group_id`,
	`primary_category_id`, `tinyurl`)

	SELECT DISTINCT NULL, c.id, a.catalog_type_id, `a`.`id`,
		1, `b`.`extension_instance_id`, `b`.`routable`, 'Item' AS `menuitem_type`,
		CONCAT( `d`.`sef_request`, '/',  a.alias), 0 AS `redirect_to_id`, 1 AS `view_group_id`,
		`b`.`primary_category_id` , '' AS `tinyurl`

		FROM `molajo_extension_instances` a,
			molajo_catalog_types b,
			molajo_applications c,
			molajo_catalog d

		WHERE d.catalog_type_id = 100000
			AND d.source_id = b.extension_instance_id
            AND a.catalog_type_id <> 1300
		    AND b.id = a.catalog_type_id
			AND a.status = 1

/** 3. Resource Model: List  */

INSERT INTO `molajo_catalog`

	(`id`, `application_id`, `catalog_type_id`, `source_id`,
		`enabled`, `extension_instance_id`, `routable`, `menuitem_type`,
		`sef_request`, `redirect_to_id`, `view_group_id`,
		`primary_category_id`, `tinyurl`)

	SELECT DISTINCT NULL, c.id, a.catalog_type_id, `b`.`extension_instance_id`,
		0, `b`.`extension_instance_id`, `b`.`routable`, 'List' AS `menuitem_type`,
		CONCAT( `b`.`alias` ), 0 AS `redirect_to_id`, 1 AS `view_group_id`,
		`b`.`primary_category_id` , '' AS `tinyurl`

		FROM `molajo_extension_instances` a,
			molajo_catalog_types b,
			molajo_applications c

		WHERE a.catalog_type_id = 12000
			AND a.id = b.extension_instance_id

/** 4. Resource Model: Items */

INSERT INTO `molajo_catalog`

	(`id`, `application_id`, `catalog_type_id`, `source_id`,
		`enabled`, `extension_instance_id`, `routable`, `menuitem_type`,
		`sef_request`, `redirect_to_id`, `view_group_id`,
		`primary_category_id`, `tinyurl`)

	SELECT DISTINCT NULL, c.id, e.catalog_type_id, `e`.`id`,
		1, `a`.`id`, 0, 'Item' AS `menuitem_type`,
		CONCAT( `b`.`alias`, '/',  e.alias), `b`.`routable`, 1,
		`b`.`primary_category_id`, '' AS `tinyurl`

		FROM `molajo_extension_instances` a,
			molajo_catalog_types b,
			molajo_applications c,
			molajo_catalog d,
			molajo_content e

		WHERE d.catalog_type_id = 12000
			AND d.source_id = a.id
			AND a.id = e.extension_instance_id
			AND a.id = b.extension_instance_id
			AND a.status = 1
			AND e.status = 1

/** 5. Items: Sites */
INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`, `extension_instance_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
	SELECT DISTINCT NULL as `id`, `b`.`id`, `a`.`id` as `extension_instance_id`, `c`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`alias`, '/', `c`.`path`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
		FROM `molajo_extension_instances` a,
			molajo_catalog_types b,
			molajo_sites c
		WHERE a.id = b.extension_instance_id
			AND b.id = c.catalog_type_id

/** 6. Items: Applications */
INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`, `extension_instance_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
	SELECT DISTINCT NULL as `id`, `b`.`id`, `a`.`id` as `extension_instance_id`, `c`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`alias`, '/', `c`.`path`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
		FROM `molajo_extension_instances` a,
			molajo_catalog_types b,
			molajo_applications c
		WHERE a.id = b.extension_instance_id
			AND b.id = c.catalog_type_id

/** 7. Items: Users */
INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`, `extension_instance_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
	SELECT DISTINCT NULL as `id`, `b`.`id`, `a`.`id` as `extension_instance_id`, `c`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`alias`, '/', `c`.`alias`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
		FROM `molajo_extension_instances` a,
			molajo_catalog_types b,
			molajo_users c
		WHERE a.id = b.extension_instance_id
			AND b.id = c.catalog_type_id

/** 6. System Extensions: Site */
INSERT INTO `molajo_site_extension_instances`(`site_id`, `extension_instance_id`, `catalog_type_id`)
	SELECT DISTINCT c.id, `a`.`source_id`, `a`.`catalog_type_id`
		FROM molajo_catalog a,
			molajo_catalog_types b,
			molajo_sites c
		WHERE a.extension_instance_id = b.extension_instance_id
			AND a.catalog_type_id = b.id
			AND a.menuitem_type = 'Item'
			AND b.model_type = 'System'

/** 7. System Extensions: Administrator Applications */
INSERT INTO `molajo_application_extension_instances`(`application_id`, `extension_instance_id`, `catalog_type_id`)
	SELECT DISTINCT c.id, `a`.`source_id`, `a`.`catalog_type_id`
		FROM molajo_catalog a,
			molajo_catalog_types b,
			molajo_applications c
	WHERE a.extension_instance_id = b.extension_instance_id
		AND a.catalog_type_id = b.id
		AND a.menuitem_type = 'Item'
		AND b.model_type = 'System'

/** for now - both applications */

/** 9. Content Extensions: Site */
INSERT INTO `molajo_site_extension_instances`(`site_id`, `extension_instance_id`, `catalog_type_id`)
	SELECT DISTINCT c.id, `a`.`extension_instance_id`, `a`.`catalog_type_id`
		FROM molajo_catalog a,
			molajo_catalog_types b,
			molajo_sites c
		WHERE a.extension_instance_id = b.extension_instance_id
			AND a.catalog_type_id = b.id
			AND a.menuitem_type = 'List'
			AND b.model_type <> 'System'

/** 10. Content Extensions: Applications */
INSERT INTO `molajo_application_extension_instances`(`application_id`, `extension_instance_id`, `catalog_type_id`)
	SELECT DISTINCT c.id, `a`.`extension_instance_id`, `a`.`catalog_type_id`
		FROM molajo_catalog a,
				molajo_catalog_types b,
				molajo_applications c
		WHERE a.extension_instance_id = b.extension_instance_id
			AND a.catalog_type_id = b.id
			AND a.menuitem_type = 'List'
			AND b.model_type <> 'System'

/** 11. System Menu Items: List */
INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`, `extension_instance_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
	SELECT DISTINCT NULL as `id`, `a`.`id`, `a`.`extension_instance_id`, `a`.`extension_instance_id` as `extension_instance_id`,  `a`.`routable`, 'List' as `menuitem_type`, CONCAT(`a`.`alias`), 0 as `redirect_to_id`, 1 as `view_group_id`, `a`.`primary_category_id`, '' as `tinyurl`
		FROM molajo_catalog_types a
			WHERE a.id = 1300

/** 12. System Menu Items: Items */
INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`, `extension_instance_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
	SELECT NULL as `id`, `a`.`catalog_type_id`, `b`.`extension_instance_id` as `extension_instance_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`alias`, '/', `a`.`id`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
		FROM `molajo_extension_instances` a,
			molajo_catalog_types b
		WHERE a.catalog_type_id = b.id
			AND a.catalog_type_id = 1300

/** 13. Content Menu Items: Items */
INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`, `extension_instance_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id`, `a`.`id`,  1 as `routable`, `menuitem_type`, `a`.`alias`, 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
	FROM `molajo_extension_instances` a,
		`molajo_catalog_types` b
	WHERE a.catalog_type_id = 1300
		AND a.catalog_type_id = b.id
		AND a.lvl = 1

/** 14+. Repeat for each level until no more found */
INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`, `extension_instance_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
	SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id`, `a`.`id`,  `b`.`routable`, `a`.`menuitem_type`,
		CASE
		WHEN `c`.`sef_request` = '' THEN `a`.`alias`
		ELSE CONCAT(`c`.`sef_request`, '/', `a`.`alias`)
		END as sef_request,
		0 as `redirect_to_id`, 1, `b`.`primary_category_id`, '' as `tinyurl`
			FROM `molajo_extension_instances` a,
				`molajo_catalog_types` b,
				`molajo_catalog` c
			WHERE a.catalog_type_id = 1300
				AND a.catalog_type_id = b.id
				AND a.lvl = 2
				AND c.source_id = a.parent_id
				AND c.catalog_type_id = 1300
				AND c.source_id <> a.id
				AND c.menuitem_type NOT IN ('list', 'item')

/** 14. System Menu Extension Instances: Site */
INSERT INTO `molajo_site_extension_instances`(`site_id`, `extension_instance_id`, `catalog_type_id`)
	SELECT DISTINCT c.id, `a`.`source_id`, `a`.`catalog_type_id`
		FROM molajo_catalog a,
			molajo_sites c
		WHERE a.catalog_type_id = 1300

/** 15. System Menu: Administrator Applications */
INSERT INTO `molajo_application_extension_instances`(`application_id`, `extension_instance_id`, `catalog_type_id`)
	SELECT DISTINCT c.id, `a`.`source_id`, `a`.`catalog_type_id`
		FROM molajo_catalog a,
			molajo_applications c
		WHERE a.catalog_type_id = 1300
			AND c.id = 2

/** 16. Site Menus */
INSERT INTO `molajo_application_extension_instances`(`application_id`, `extension_instance_id`, `catalog_type_id`)
	SELECT DISTINCT c.id, `a`.`source_id`, `a`.`catalog_type_id`
		FROM molajo_catalog a,
			molajo_extension_instances b,
			molajo_applications c
		WHERE a.catalog_type_id = 1300
			AND c.id = 1
			AND b.id = a.source_id
			AND b.extension_id IN (3500, 3550)

/** 28. Build Catalog Categories Table */
INSERT INTO `molajo_catalog_categories`(`catalog_id`, `category_id`)
	SELECT DISTINCT a.id, a.primary_category_id
		FROM molajo_catalog a

/** 29. Build Catalog Activity */
INSERT INTO `molajo_catalog_activity`(`id`, `catalog_id`, `user_id`, `action_id`, `rating`, `activity_datetime`, `ip_address`, `customfields`)
	SELECT NULL, a.id, 1, 2, NULL, '2012-07-01 12:00:00', '127.0.0.1', '{}'
		FROM molajo_catalog a

/** 30. Build View Group Permissions */
INSERT INTO `molajo_view_group_permissions`(`id`, `view_group_id`, `catalog_id`, `action_id`)
	SELECT DISTINCT NULL, a.view_group_id, a.id, '3'
		FROM molajo_catalog a

/** 31. User Activity */
INSERT INTO `molajo_user_activity`(`id`, `user_id`, `action_id`, `catalog_id`, `activity_datetime`, `ip_address`)
	SELECT NULL, 1, 2, id, '2012-07-01 12:00:00', '127.0.0.1'
		FROM molajo_catalog a
