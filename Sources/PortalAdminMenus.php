<?php

/**
 * @package SimplePortal
 *
 * @author SimplePortal Team
 * @copyright 2013 SimplePortal Team
 * @license BSD 3-clause 
 *
 * @version 2.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function sportal_admin_menus_main()
{
	global $context, $sourcedir, $txt;

	if (!allowedTo('sp_admin'))
		isAllowedTo('sp_manage_menus');

	require_once($sourcedir . '/Subs-PortalAdmin.php');

	loadTemplate('PortalAdminMenus');

	$sub_actions = array(
		'listmainitem' => 'sportal_admin_menus_main_item_list',
		'addmainitem' => 'sportal_admin_menus_main_item_edit',
		'editmainitem' => 'sportal_admin_menus_main_item_edit',
		'deletemainitem' => 'sportal_admin_menus_main_item_delete',
		'listcustommenu' => 'sportal_admin_menus_custom_menu_list',
		'addcustommenu' => 'sportal_admin_menus_custom_menu_edit',
		'editcustommenu' => 'sportal_admin_menus_custom_menu_edit',
		'deletecustommenu' => 'sportal_admin_menus_custom_menu_delete',
		'listcustomitem' => 'sportal_admin_menus_custom_item_list',
		'addcustomitem' => 'sportal_admin_menus_custom_item_edit',
		'editcustomitem' => 'sportal_admin_menus_custom_item_edit',
		'deletecustomitem' => 'sportal_admin_menus_custom_item_delete',
	);

	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($sub_actions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'listcustommenu';

	$context['sub_action'] = $_REQUEST['sa'];

	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['sp_admin_menus_title'],
		'help' => 'sp_MenusArea',
		'description' => $txt['sp_admin_menus_desc'],
		'tabs' => array(
			'listmainitem' => array(
			),
			'addmainitem' => array(
			),
			'listcustommenu' => array(
			),
			'addcustommenu' => array(
			),
		),
	);

	if ($context['sub_action'] == 'listcustomitem' && !empty($_REQUEST['menu_id']))
	{
		$context[$context['admin_menu_name']]['tab_data']['tabs']['addcustomitem'] = array(
			'add_params' => ';menu_id=' . $_REQUEST['menu_id'],
		);
	}

	$sub_actions[$context['sub_action']]();
}

function sportal_admin_menus_custom_menu_list()
{
	global $smcFunc, $context, $scripturl, $txt;

	if (!empty($_POST['remove_menus']) && !empty($_POST['remove']) && is_array($_POST['remove']))
	{
		checkSession();

		foreach ($_POST['remove'] as $index => $menu_id)
			$_POST['remove'][(int) $index] = (int) $menu_id;

		$smcFunc['db_query']('','
			DELETE FROM {db_prefix}sp_custom_menus
			WHERE id_menu IN ({array_int:menus})',
			array(
				'menus' => $_POST['remove'],
			)
		);
	}

	$sort_methods = array(
		'name' =>  array(
			'down' => 'cm.name ASC',
			'up' => 'cm.name DESC'
		),
	);

	$context['columns'] = array(
		'name' => array(
			'width' => '75%',
			'label' => $txt['sp_admin_menus_col_name'],
			'class' => 'first_th',
			'sortable' => true
		),
		'items' => array(
			'width' => '10%',
			'label' => $txt['sp_admin_menus_col_items'],
			'sortable' => true
		),
		'actions' => array(
			'width' => '15%',
			'label' => $txt['sp_admin_menus_col_actions'],
			'sortable' => false
		),
	);

	if (!isset($_REQUEST['sort']) || !isset($sort_methods[$_REQUEST['sort']]))
		$_REQUEST['sort'] = 'name';

	foreach ($context['columns'] as $col => $dummy)
	{
		$context['columns'][$col]['selected'] = $col == $_REQUEST['sort'];
		$context['columns'][$col]['href'] = $scripturl . '?action=admin;area=portalmenus;sa=listcustommenu;sort=' . $col;

		if (!isset($_REQUEST['desc']) && $col == $_REQUEST['sort'])
			$context['columns'][$col]['href'] .= ';desc';

		$context['columns'][$col]['link'] = '<a href="' . $context['columns'][$col]['href'] . '">' . $context['columns'][$col]['label'] . '</a>';
	}

	$context['sort_by'] = $_REQUEST['sort'];
	$context['sort_direction'] = !isset($_REQUEST['desc']) ? 'down' : 'up';

	$request = $smcFunc['db_query']('','
		SELECT COUNT(*)
		FROM {db_prefix}sp_custom_menus'
	);
	list ($total_menus) =  $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	$context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=portalmenus;sa=listcustommenu;sort=' . $_REQUEST['sort'] . (isset($_REQUEST['desc']) ? ';desc' : ''), $_REQUEST['start'], $total_menus, 20);
	$context['start'] = $_REQUEST['start'];

	$request = $smcFunc['db_query']('','
		SELECT cm.id_menu, cm.name, COUNT(mi.id_item) AS items
		FROM {db_prefix}sp_custom_menus AS cm
			LEFT JOIN {db_prefix}sp_menu_items AS mi ON (mi.id_menu = cm.id_menu)
		GROUP BY cm.id_menu
		ORDER BY {raw:sort}
		LIMIT {int:start}, {int:limit}',
		array(
			'sort' => $sort_methods[$_REQUEST['sort']][$context['sort_direction']],
			'start' => $context['start'],
			'limit' => 20,
		)
	);
	$context['menus'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$context['menus'][$row['id_menu']] = array(
			'id' => $row['id_menu'],
			'name' => $row['name'],
			'items' => $row['items'],
			'actions' => array(
				'add' => '<a href="' . $scripturl . '?action=admin;area=portalmenus;sa=addcustomitem;menu_id=' . $row['id_menu'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . sp_embed_image('add') . '</a>',
				'items' => '<a href="' . $scripturl . '?action=admin;area=portalmenus;sa=listcustomitem;menu_id=' . $row['id_menu'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . sp_embed_image('items') . '</a>',
				'edit' => '<a href="' . $scripturl . '?action=admin;area=portalmenus;sa=editcustommenu;menu_id=' . $row['id_menu'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . sp_embed_image('modify') . '</a>',
				'delete' => '<a href="' . $scripturl . '?action=admin;area=portalmenus;sa=deletecustommenu;menu_id=' . $row['id_menu'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(\'', $txt['sp_admin_menus_menu_delete_confirm'], '\');">' . sp_embed_image('delete') . '</a>',
			)
		);
	}
	$smcFunc['db_free_result']($request);

	$context['sub_template'] = 'menus_custom_menu_list';
	$context['page_title'] = $txt['sp_admin_menus_custom_menu_list'];
}

function sportal_admin_menus_custom_menu_edit()
{
	global $smcFunc, $context, $txt;

	$context['is_new'] = empty($_REQUEST['menu_id']);

	if (!empty($_POST['submit']))
	{
		checkSession();

		if (!isset($_POST['name']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['name'], ENT_QUOTES)) === '')
			fatal_lang_error('sp_error_menu_name_empty', false);

		$fields = array(
			'name' => 'string',
		);

		$menu_info = array(
			'id' => (int) $_POST['menu_id'],
			'name' => $smcFunc['htmlspecialchars']($_POST['name'], ENT_QUOTES),
		);

		if ($context['is_new'])
		{
			unset($menu_info['id']);

			$smcFunc['db_insert']('',
				'{db_prefix}sp_custom_menus',
				$fields,
				$menu_info,
				array('id_menu')
			);
			$menu_info['id'] = $smcFunc['db_insert_id']('{db_prefix}sp_custom_menus', 'id_menu');
		}
		else
		{
			$update_fields = array();
			foreach ($fields as $name => $type)
				$update_fields[] = $name . ' = {' . $type . ':' . $name . '}';

			$smcFunc['db_query']('','
				UPDATE {db_prefix}sp_custom_menus
				SET ' . implode(', ', $update_fields) . '
				WHERE id_menu = {int:id}',
				$menu_info
			);
		}

		redirectexit('action=admin;area=portalmenus;sa=listcustommenu');
	}

	if ($context['is_new'])
	{
		$context['menu'] = array(
			'id' => 0,
			'name' => $txt['sp_menus_default_custom_menu_name'],
		);
	}
	else
	{
		$_REQUEST['menu_id'] = (int) $_REQUEST['menu_id'];
		$context['menu'] = sportal_get_custom_menus($_REQUEST['menu_id']);
	}

	$context['page_title'] = $context['is_new'] ? $txt['sp_admin_menus_custom_menu_add'] : $txt['sp_admin_menus_custom_menu_edit'];
	$context['sub_template'] = 'menus_custom_menu_edit';
}

function sportal_admin_menus_custom_menu_delete()
{
	global $smcFunc;

	checkSession('get');

	$menu_id = !empty($_REQUEST['menu_id']) ? (int) $_REQUEST['menu_id'] : 0;

	$smcFunc['db_query']('','
		DELETE FROM {db_prefix}sp_menu_items
		WHERE id_menu = {int:id}',
		array(
			'id' => $menu_id,
		)
	);

	$smcFunc['db_query']('','
		DELETE FROM {db_prefix}sp_custom_menus
		WHERE id_menu = {int:id}',
		array(
			'id' => $menu_id,
		)
	);

	redirectexit('action=admin;area=portalmenus;sa=listcustommenu');
}

function sportal_admin_menus_custom_item_list()
{
	global $smcFunc, $context, $scripturl, $txt;

	if (!empty($_POST['remove_items']) && !empty($_POST['remove']) && is_array($_POST['remove']))
	{
		checkSession();

		foreach ($_POST['remove'] as $index => $item_id)
			$_POST['remove'][(int) $index] = (int) $item_id;

		$smcFunc['db_query']('','
			DELETE FROM {db_prefix}sp_menu_items
			WHERE id_item IN ({array_int:items})',
			array(
				'items' => $_POST['remove'],
			)
		);
	}

	$menu_id = !empty($_REQUEST['menu_id']) ? (int) $_REQUEST['menu_id'] : 0;
	$context['menu'] = sportal_get_custom_menus($menu_id);

	if (empty($context['menu']))
	{
		fatal_lang_error('error_sp_menu_not_found', false);
	}

	$context['columns'] = array(
		'title' => array(
			'width' => '45%',
			'label' => $txt['sp_admin_menus_col_title'],
			'class' => 'first_th',
		),
		'namespace' => array(
			'width' => '25%',
			'label' => $txt['sp_admin_menus_col_namespace'],
		),
		'target' => array(
			'width' => '15%',
			'label' => $txt['sp_admin_menus_col_target'],
		),
		'actions' => array(
			'width' => '15%',
			'label' => $txt['sp_admin_menus_col_actions'],
		),
	);

	$request = $smcFunc['db_query']('','
		SELECT id_item, title, namespace, target
		FROM {db_prefix}sp_menu_items
		WHERE id_menu = {int:menu}
		ORDER BY title',
		array(
			'menu' => $menu_id,
		)
	);
	$context['items'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$context['items'][$row['id_item']] = array(
			'id' => $row['id_item'],
			'title' => $row['title'],
			'namespace' => $row['namespace'],
			'target' => $txt['sp_admin_menus_link_target_' . $row['target']],
			'actions' => array(
				'edit' => '<a href="' . $scripturl . '?action=admin;area=portalmenus;sa=editcustomitem;menu_id=' . $context['menu']['id'] . ';item_id=' . $row['id_item'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . sp_embed_image('modify') . '</a>',
				'delete' => '<a href="' . $scripturl . '?action=admin;area=portalmenus;sa=deletecustomitem;menu_id=' . $context['menu']['id'] . ';item_id=' . $row['id_item'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(\'', $txt['sp_admin_menus_item_delete_confirm'], '\');">' . sp_embed_image('delete') . '</a>',
			)
		);
	}
	$smcFunc['db_free_result']($request);

	$context['sub_template'] = 'menus_custom_item_list';
	$context['page_title'] = $txt['sp_admin_menus_custom_item_list'];
}

function sportal_admin_menus_custom_item_edit()
{
	global $smcFunc, $context, $txt;

	$menu_id = !empty($_REQUEST['menu_id']) ? (int) $_REQUEST['menu_id'] : 0;
	$context['menu'] = sportal_get_custom_menus($menu_id);

	if (empty($context['menu']))
	{
		fatal_lang_error('error_sp_menu_not_found', false);
	}

	$context['is_new'] = empty($_REQUEST['item_id']);

	if (!empty($_POST['submit']))
	{
		checkSession();

		if (!isset($_POST['title']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['title'], ENT_QUOTES)) === '')
			fatal_lang_error('sp_error_item_title_empty', false);

		if (!isset($_POST['namespace']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['namespace'], ENT_QUOTES)) === '')
			fatal_lang_error('sp_error_item_namespace_empty', false);

		$result = $smcFunc['db_query']('','
			SELECT id_item
			FROM {db_prefix}sp_menu_items
			WHERE namespace = {string:namespace}
				AND id_item != {int:current}
			LIMIT {int:limit}',
			array(
				'namespace' => $smcFunc['htmlspecialchars']($_POST['namespace'], ENT_QUOTES),
				'current' => (int) $_POST['item_id'],
				'limit' => 1,
			)
		);
		list ($has_duplicate) = $smcFunc['db_fetch_row']($result);
		$smcFunc['db_free_result']($result);

		if (!empty($has_duplicate))
			fatal_lang_error('sp_error_item_namespace_duplicate', false);

		if (preg_match('~[^a-z0-9_]+~', $_POST['namespace']) != 0)
			fatal_lang_error('sp_error_item_namespace_invalid_chars', false);

		if (preg_replace('~[0-9]+~', '', $_POST['namespace']) === '')
			fatal_lang_error('sp_error_item_namespace_numeric', false);

		$fields = array(
			'id_menu' => 'int',
			'namespace' => 'string',
			'title' => 'string',
			'url' => 'string',
			'target' => 'int',
		);

		$item_info = array(
			'id' => (int) $_POST['item_id'],
			'id_menu' => $context['menu']['id'],
			'namespace' => $smcFunc['htmlspecialchars']($_POST['namespace'], ENT_QUOTES),
			'title' => $smcFunc['htmlspecialchars']($_POST['title'], ENT_QUOTES),
			'url' => $smcFunc['htmlspecialchars']($_POST['url'], ENT_QUOTES),
			'target' => (int) $_POST['target'],
		);

		$link_type = !empty($_POST['link_type']) ? $_POST['link_type'] : '';
		$link_item = !empty($_POST['link_item']) ? $_POST['link_item'] : '';

		if ($link_type != 'custom')
		{
			if (preg_match('~^\d+|[A-Za-z0-9_\-]+$~', $link_item, $match))
			{
				$link_item_id = $match[0];
			}
			else
				fatal_lang_error('sp_error_item_link_item_invalid', false);

			switch ($link_type)
			{
				case 'action':
				case 'page':
				case 'category':
				case 'article':
					$item_info['url'] = '$scripturl?' . $link_type . '=' . $link_item_id;
					break;
				case 'board':
					$item_info['url'] = '$scripturl?' . $link_type . '=' . $link_item_id . '.0';
					break;
			}
		}

		if ($context['is_new'])
		{
			unset($item_info['id']);

			$smcFunc['db_insert']('',
				'{db_prefix}sp_menu_items',
				$fields,
				$item_info,
				array('id_item')
			);
			$item_info['id'] = $smcFunc['db_insert_id']('{db_prefix}sp_menu_items', 'id_item');
		}
		else
		{
			$update_fields = array();
			foreach ($fields as $name => $type)
				$update_fields[] = $name . ' = {' . $type . ':' . $name . '}';

			$smcFunc['db_query']('','
				UPDATE {db_prefix}sp_menu_items
				SET ' . implode(', ', $update_fields) . '
				WHERE id_item = {int:id}',
				$item_info
			);
		}

		redirectexit('action=admin;area=portalmenus;sa=listcustomitem;menu_id=' . $context['menu']['id']);
	}

	if ($context['is_new'])
	{
		$context['item'] = array(
			'id' => 0,
			'namespace' => 'item' . mt_rand(1, 5000),
			'title' => $txt['sp_menus_default_menu_item_name'],
			'url' => '',
			'target' => 0,
		);
	}
	else
	{
		$_REQUEST['item_id'] = (int) $_REQUEST['item_id'];
		$context['item'] = sportal_get_menu_items($_REQUEST['item_id']);
	}

	$context['items']['action'] = array(
		'portal' => $txt['sp-portal'],
		'forum' => $txt['sp-forum'],
		'recent' => $txt['recent_posts'],
		'unread' => $txt['unread_topics_visit'],
		'unreadreplies' => $txt['unread_replies'],
		'profile' => $txt['profile'],
		'pm' => $txt['pm_short'],
		'calendar' => $txt['calendar'],
		'admin' =>  $txt['admin'],
		'login' =>  $txt['login'],
		'register' =>  $txt['register'],
		'post' =>  $txt['post'],
		'stats' =>  $txt['forum_stats'],
		'search' =>  $txt['search'],
		'mlist' =>  $txt['members_list'],
		'moderate' =>  $txt['moderate'],
		'help' =>  $txt['help'],
		'who' =>  $txt['who_title'],
	);

	$request = $smcFunc['db_query']('','
		SELECT id_board, name
		FROM {db_prefix}boards
		WHERE redirect = {string:empty}
		ORDER BY name DESC',
		array(
			'empty' => '',
		)
	);
	$context['items']['board'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['items']['board'][$row['id_board']] = $row['name'];
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query']('','
		SELECT id_page, title
		FROM {db_prefix}sp_pages
		ORDER BY title DESC'
	);
	$context['items']['page'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['items']['page'][$row['id_page']] = $row['title'];
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query']('','
		SELECT id_category, name
		FROM {db_prefix}sp_categories
		ORDER BY name DESC'
	);
	$context['items']['category'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['items']['category'][$row['id_category']] = $row['name'];
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query']('','
		SELECT id_article, title
		FROM {db_prefix}sp_articles
		ORDER BY title DESC'
	);
	$context['items']['article'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['items']['article'][$row['id_article']] = $row['title'];
	$smcFunc['db_free_result']($request);

	$context['page_title'] = $context['is_new'] ? $txt['sp_admin_menus_custom_item_add'] : $txt['sp_admin_menus_custom_item_edit'];
	$context['sub_template'] = 'menus_custom_item_edit';
}

function sportal_admin_menus_custom_item_delete()
{
	global $smcFunc;

	checkSession('get');

	$menu_id = !empty($_REQUEST['menu_id']) ? (int) $_REQUEST['menu_id'] : 0;
	$item_id = !empty($_REQUEST['item_id']) ? (int) $_REQUEST['item_id'] : 0;

	$smcFunc['db_query']('','
		DELETE FROM {db_prefix}sp_menu_items
		WHERE id_item = {int:id}',
		array(
			'id' => $item_id,
		)
	);

	redirectexit('action=admin;area=portalmenus;sa=listcustomitem;menu_id=' . $menu_id);
}