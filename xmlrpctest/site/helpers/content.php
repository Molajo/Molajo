<?php
/**
 * @version		$Id: query.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Content Component Query Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class XmlrpctestHelperContent
{
	function _get_xmlrpc_url () {
		$params = &JComponentHelper::getParams( 'com_xmlrpctest' );
		$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';
		return $moodle_xmlrpc_server_url;
	}

	function call_method ($method, $params = '')
	{
		$moodle_xmlrpc_server_url = XmlrpctestHelperContent::_get_xmlrpc_url ();

		$request = xmlrpc_encode_request("auth/xmlrpctest/auth.php/$method", array ($params));
		$context = stream_context_create(array('http' => array(
					'method' => "POST",
					'header' => "Content-Type: text/xml ",
					'content' => $request
						)));
		$file = file_get_contents($moodle_xmlrpc_server_url , false, $context);
		$response = xmlrpc_decode($file);

		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
				die; // XXX Something softer?
			}

		return $response;
	}

	function getCourseEvents($id)
	{
		return XmlrpctestHelperContent::call_method ('get_upcoming_events', $id);
	}

	function getCourseInfo ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_course_info', $id);
	}

	function getCourseCategories ()
	{
		return XmlrpctestHelperContent::call_method ('get_course_categories');
	}

	function getCourseCategory ($id)
	{
		return XmlrpctestHelperContent::call_method ('courses_by_category', $id);
	}

	function getCourseNews ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_news_items', $id);
	}

	function getCourseStudentsNo ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_course_students_no', $id);
	}

	function getAssignmentSubmissions ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_assignment_submissions', $id);
	}

	function getAssignmentGrades ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_assignment_grades', $id);
	}

	function getCourseDailyStats ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_course_daily_stats', $id);
	}

	function getCourseList ($enrollable_only)
	{
		return XmlrpctestHelperContent::call_method ('list_courses', $enrollable_only);
	}

	function getStudentsNo ()
	{
		return XmlrpctestHelperContent::call_method ('get_student_no');
	}

	function getCoursesNo ()
	{
		return XmlrpctestHelperContent::call_method ('get_course_no');
	}

	function getEnrollableCoursesNo ()
	{
		return XmlrpctestHelperContent::call_method ('get_enrollable_course_no');
	}

	function getAssignmentsNo ()
	{
		return XmlrpctestHelperContent::call_method ('get_total_assignment_submissions');
	}

	function getLastWeekStats ()
	{
		return XmlrpctestHelperContent::call_method ('get_site_last_week_stats');
	}

	function getCourseTeachers ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_course_editing_teachers', $id);
	}

	function getCourseContents ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_course_contents', $id);
	}


	function getMyEvents ()
	{

		$user = & JFactory::getUser();
		$id = $user->get('id');
		$username = $user->get('username');

		$cursos = XmlrpctestHelperContent::call_method ('my_courses', $username);

		/* Para cada curso, obtenemos todos los eventos */
		$i = 0;
		foreach ($cursos as $id => $curso) {
			$id = $curso['id'];
			$course_events[$i]['events'] = XmlrpctestHelperContent::getCourseEvents ($id);
			$course_events[$i]['info'] = XmlrpctestHelperContent::getCourseInfo ($id);
			$i++;
		}

		return ($course_events);

	}

	function getMyNews ()
	{
		$user = & JFactory::getUser();
		$id = $user->get('id');
		$username = $user->get('username');

		$cursos = XmlrpctestHelperContent::call_method ('my_courses', $username);

		/* Para cada curso, obtenemos todas las noticias */
		$i = 0;
		foreach ($cursos as $id => $curso) {
			$id = $curso['id'];
			$course_news[$i]['news'] = XmlrpctestHelperContent::getCourseNews ($id);
			$course_news[$i]['info'] = XmlrpctestHelperContent::getCourseInfo ($id);
			$i++;
		}

		return ($course_news);

	}

	function getCourseGradeCategories ($id)
	{
		return XmlrpctestHelperContent::call_method ('get_course_grade_categories', $id);
	}

	function getJumpURL ()
	{
		$params = &JComponentHelper::getParams( 'com_xmlrpctest' );
		$moodle_auth_land_url = $params->get( 'MOODLE_URL' ).'/auth/xmlrpctest/land.php';

		 $linkstarget = $params->get( 'linkstarget' );
		 if ($linkstarget == 'wrapper')
			 $use_wrapper = 1;
		 else $use_wrapper = 0;

		$user = & JFactory::getUser();
		$id = $user->get('id');
		$username = $user->get('username');


		$db           =& JFactory::getDBO();
		$query = 'SELECT session_id' .
			' FROM #__session' .
			' WHERE userid =';
		$query .= "'$id'";
		$db->setQuery($query);
		$sessions = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		if (count($sessions))
		foreach ($sessions as $session)
			$token = md5 ($session->session_id);

                $jump_url = $moodle_auth_land_url."?username=$username&token=$token&use_wrapper=$use_wrapper";

		return $jump_url;
	}

	function getMenuItem ()
	{
		$menu = &JSite::getMenu();
		$menuItem = &$menu->getActive();

		if (!$menuItem)
			return;

		$itemid = $menuItem->id;

		return $itemid;
	}
}
