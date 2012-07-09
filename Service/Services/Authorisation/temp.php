<?php

/** Source Data */

// Prepare Source Data Row

	/**
	 * Prepare Group Permissions Rows by 1) accepting input or 2) copying from parent
	 *
	 * Create - 2
	 * Read - 3
	 * Update - 4
	 * Publish - 5
	 * Delete - 6
	 * Administer - 7
 	 */

     // Prepare array of permissions for group_permissions table
		// Priority 1: primary category in the request
		// Priority 2: permissions associated with parent in source
		// Priority 3: resource permissions
		// Priority 4: default application CRUD permissions

	 // Verify with Access Control System, if necessary it will create
	//  a View Access row and send back the key

	/** Create Catalog Entry with View Access Key */

	/** Create Group Permissions Rows */

/** Application */
INSERT INTO `molajo_view_group_permissions` (view_group_id, catalog_id, action_id)

SELECT b.view_group_id, c.id, c.id

FROM `molajo_catalog` a,
 `molajo_actions` b
WHERE a.`catalog_type_id` in (100, 120)
	AND a.id = b.group_id
		AND b.view_group_id = c.view_group_id
			AND d.title = 'view' ;

