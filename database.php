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

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$_GET['debug'] = 'Blue Dream!';
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $db_prefix, $db_package_log;

if (!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$tables = array(
	'sp_articles' => array(
		'columns' => array(
			array('name' => 'id_article', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'id_category', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'id_member', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'member_name', 'type' => 'varchar', 'size' => 80, 'default' => ''),
			array('name' => 'namespace', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'title', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'body', 'type' => 'mediumtext'),
			array('name' => 'type', 'type' => 'varchar', 'size' => 40, 'default' => ''),
			array('name' => 'date', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'permissions', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'styles', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'views', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'comments', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'status', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_article')),
		),
	),
	'sp_blocks' => array(
		'columns' => array(
			array('name' => 'id_block', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'label', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'type', 'type' => 'varchar', 'size' => 40, 'default' => ''),
			array('name' => 'col', 'type' => 'tinyint', 'size' => 4, 'default' => 0),
			array('name' => 'row', 'type' => 'tinyint', 'size' => 4, 'default' => 0),
			array('name' => 'permissions', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'styles', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'visibility', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'state', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
			array('name' => 'force_view', 'type' => 'tinyint', 'size' => 2, 'default' => 0),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_block')),
			array('type' => 'index', 'columns' => array('state')),
		),
	),
	'sp_categories' => array(
		'columns' => array(
			array('name' => 'id_category', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'namespace', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'name', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'description', 'type' => 'text'),
			array('name' => 'permissions', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'articles', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'status', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_category')),
		),
	),
	'sp_comments' => array(
		'columns' => array(
			array('name' => 'id_comment', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'id_article', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'id_member', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'member_name', 'type' => 'varchar', 'size' => 80, 'default' => ''),
			array('name' => 'body', 'type' => 'text'),
			array('name' => 'log_time', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_comment')),
		),
	),
	'sp_custom_menus' => array(
		'columns' => array(
			array('name' => 'id_menu', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'name', 'type' => 'varchar', 'size' => 255, 'default' => ''),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_menu')),
		),
	),
	'sp_functions' => array(
		'columns' => array(
			array('name' => 'id_function', 'type' => 'tinyint', 'size' => 4, 'auto' => true, 'unsigned' => true),
			array('name' => 'function_order', 'type' => 'tinyint', 'size' => 4, 'default' => 0),
			array('name' => 'name', 'type' => 'varchar', 'size' => 40, 'default' => ''),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_function')),
		),
	),
	'sp_menu_items' => array(
		'columns' => array(
			array('name' => 'id_item', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'id_menu', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'namespace', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'title', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'href', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'target', 'type' => 'tinyint', 'size' => 4, 'default' => 0),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_item')),
		),
	),
	'sp_pages' => array(
		'columns' => array(
			array('name' => 'id_page', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'namespace', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'title', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'body', 'type' => 'mediumtext'),
			array('name' => 'type', 'type' => 'varchar', 'size' => 40, 'default' => ''),
			array('name' => 'permissions', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'styles', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'views', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'status', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_page')),
		),
	),
	'sp_parameters' => array(
		'columns' => array(
			array('name' => 'id_block', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'variable', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'value', 'type' => 'text'),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_block', 'variable')),
			array('type' => 'key', 'columns' => array('variable')),
		),
	),
	'sp_profiles' => array(
		'columns' => array(
			array('name' => 'id_profile', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'type', 'type' => 'tinyint', 'size' => 4, 'default' => 0),
			array('name' => 'name', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'value', 'type' => 'text'),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id_profile')),
		),
	),
	'sp_shoutboxes' => array(
		'columns' => array(
			array('name' => 'id_shoutbox', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'name', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'permissions', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'moderator_groups', 'type' => 'text'),
			array('name' => 'warning', 'type' => 'text'),
			array('name' => 'allowed_bbc', 'type' => 'text'),
			array('name' => 'height', 'type' => 'smallint', 'size' => 5, 'default' => 200, 'unsigned' => true),
			array('name' => 'num_show', 'type' => 'smallint', 'size' => 5, 'default' => 20, 'unsigned' => true),
			array('name' => 'num_max', 'type' => 'mediumint', 'size' => 8, 'default' => 1000, 'unsigned' => true),
			array('name' => 'refresh', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
			array('name' => 'reverse', 'type' => 'tinyint', 'size' => 4, 'default' => 0),
			array('name' => 'caching', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
			array('name' => 'status', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
			array('name' => 'num_shouts', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'last_update', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
		),
		'indexes' => array(
			array('type' => 'primary','columns' => array('id_shoutbox')),
		),
	),
	'sp_shouts' => array(
		'columns' => array(
			array('name' => 'id_shout', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'id_shoutbox', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'id_member', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'member_name', 'type' => 'varchar', 'size' => 80, 'default' => ''),
			array('name' => 'log_time', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'body', 'type' => 'text'),
		),
		'indexes' => array(
			array('type' => 'primary','columns' => array('id_shout')),
		),
	),
);

foreach ($tables as $table => $data)
	$smcFunc['db_create_table']('{db_prefix}' . $table, $data['columns'], $data['indexes'], array(), 'ignore');

$smcFunc['db_insert']('ignore',
	'{db_prefix}sp_functions',
	array('id_function' => 'int', 'function_order' => 'int', 'name' => 'string'),
	array(
		array(1, 1, 'sp_userInfo'),
		array(2, 2, 'sp_latestMember'),
		array(3, 3, 'sp_whosOnline'),
		array(5, 4, 'sp_boardStats'),
		array(7, 5, 'sp_topPoster'),
		array(35, 6, 'sp_topStatsMember'),
		array(23, 7, 'sp_recent'),
		array(9, 8, 'sp_topTopics'),
		array(8, 9, 'sp_topBoards'),
		array(4, 10, 'sp_showPoll'),
		array(12, 11, 'sp_boardNews'),
		array(6, 12, 'sp_quickSearch'),
		array(13, 13, 'sp_news'),
		array(20, 14, 'sp_attachmentImage'),
		array(21, 15, 'sp_attachmentRecent'),
		array(27, 16, 'sp_calendar'),
		array(24, 17, 'sp_calendarInformation'),
		array(25, 18, 'sp_rssFeed'),
		array(28, 19, 'sp_theme_select'),
		array(29, 20, 'sp_staff'),
		array(31, 21, 'sp_articles'),
		array(36, 22, 'sp_shoutbox'),
		array(26, 23, 'sp_gallery'),
		array(32, 24, 'sp_arcade'),
		array(33, 25, 'sp_shop'),
		array(30, 26, 'sp_blog'),
		array(10, 27, 'sp_menu'),
		array(19, 98, 'sp_bbc'),
		array(17, 99, 'sp_html'),
		array(18, 100, 'sp_php'),
	),
	array('id_function')
);

$result = $smcFunc['db_query']('','
	SELECT id_block
	FROM {db_prefix}sp_blocks
	LIMIT 1',
	array(
	)
);
list ($has_block) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($has_block))
{
	$welcome_text = '<h2 style="text-align: center;">Welcome to SimplePortal!</h2>
<p>SimplePortal is one of several portal mods for Simple Machines Forum (SMF). Although always developing, SimplePortal is produced with the user in mind first. User feedback is the number one method of growth for SimplePortal, and our users are always finding ways for SimplePortal to grow. SimplePortal stays competative with other portal software by adding numerous user-requested features such as articles, block types and the ability to completely customize the portal page.</p>
<p>All this and SimplePortal has remained Simple! SimplePortal is built for simplicity and ease of use; ensuring the average forum administrator can install SimplePortal, configure a few settings, and show off the brand new portal to the users in minutes. Confusing menus, undesired pre-loaded blocks and settings that cannot be found are all avoided as much as possible. Because when it comes down to it, SimplePortal is YOUR portal, and should reflect your taste as much as possible.</p>';

	$default_blocks = array(
		'user_info' => array('label' => 'User Info', 'type' => 'sp_userInfo', 'col' => 1, 'row' => 1, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'whos_online' => array('label' => 'Who&#039;s Online', 'type' => 'sp_whosOnline', 'col' => 1, 'row' => 2, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'board_stats' => array('label' => 'Board Stats', 'type' => 'sp_boardStats', 'col' => 1, 'row' => 3, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'theme_select' => array('label' => 'Theme Select', 'type' => 'sp_theme_select', 'col' => 1, 'row' => 4, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'search' => array('label' => 'Search', 'type' => 'sp_quickSearch', 'col' => 1, 'row' => 5, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'news' => array('label' => 'News', 'type' => 'sp_news', 'col' => 2, 'row' => 1, 'permissions' => 3, 'styles' => 8, 'visibility' => 14),
		'welcome' => array('label' => 'Welcome', 'type' => 'sp_html', 'col' => 2, 'row' => 2, 'permissions' => 3, 'styles' => 8, 'visibility' => 14),
		'board_news' => array('label' => 'Board News', 'type' => 'sp_boardNews', 'col' => 2, 'row' => 3, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'recent_topics' => array('label' => 'Recent Topics', 'type' => 'sp_recent', 'col' => 3, 'row' => 1, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'top_poster' => array('label' => 'Top Poster', 'type' => 'sp_topPoster', 'col' => 4, 'row' => 1, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'recent_posts' => array('label' => 'Recent Posts', 'type' => 'sp_recent', 'col' => 4, 'row' => 2, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'staff' => array('label' => 'Forum Staff', 'type' => 'sp_staff', 'col' => 4, 'row' => 3, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'calendar' => array('label' => 'Calendar', 'type' => 'sp_calendar', 'col' => 4, 'row' => 4, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
		'top_boards' => array('label' => 'Top Boards', 'type' => 'sp_topBoards', 'col' => 4, 'row' => 5, 'permissions' => 3, 'styles' => 4, 'visibility' => 14),
	);

	$smcFunc['db_insert']('ignore',
		'{db_prefix}sp_blocks',
		array('label' => 'text', 'type' => 'text', 'col' => 'int', 'row' => 'int', 'permissions' => 'int', 'styles' => 'int', 'visibility' => 'int'),
		$default_blocks,
		array('id_block', 'state')
	);

	$request = $smcFunc['db_query']('', '
		SELECT MIN(id_block) AS id, type
		FROM {db_prefix}sp_blocks
		WHERE type IN ({array_string:types})
		GROUP BY type
		LIMIT 4',
		array(
			'types' => array('sp_html', 'sp_boardNews', 'sp_calendar', 'sp_recent'),
		)
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$block_ids[$row['type']] = $row['id'];
	$smcFunc['db_free_result']($request);

	$default_parameters = array(
		array('id_block' => $block_ids['sp_html'], 'variable' => 'content', 'value' => htmlspecialchars($welcome_text)),
		array('id_block' => $block_ids['sp_boardNews'], 'variable' => 'avatar', 'value' => 1),
		array('id_block' => $block_ids['sp_boardNews'], 'variable' => 'per_page', 'value' => 3),
		array('id_block' => $block_ids['sp_calendar'], 'variable' => 'events', 'value' => 1),
		array('id_block' => $block_ids['sp_calendar'], 'variable' => 'birthdays', 'value' => 1),
		array('id_block' => $block_ids['sp_calendar'], 'variable' => 'holidays', 'value' => 1),
		array('id_block' => $block_ids['sp_recent'], 'variable' => 'type', 'value' => 1),
		array('id_block' => $block_ids['sp_recent'], 'variable' => 'display', 'value' => 1),
	);

	$smcFunc['db_insert']('replace',
		'{db_prefix}sp_parameters',
		array('id_block' => 'int', 'variable' => 'text', 'value' => 'text'),
		$default_parameters,
		array('id_block', 'variable')
	);
}

$result = $smcFunc['db_query']('','
	SELECT id_profile
	FROM {db_prefix}sp_profiles
	WHERE type = {int:type}
	LIMIT {int:limit}',
	array(
		'type' => 1,
		'limit' => 1,
	)
);
list ($has_permission_profiles) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($has_permission_profiles))
{
	$request = $smcFunc['db_query']('', '
		SELECT id_group
		FROM {db_prefix}membergroups
		WHERE min_posts != {int:min_posts}',
		array(
			'min_posts' => -1,
		)
	);
	$post_groups = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$post_groups[] = $row['id_group'];
	$smcFunc['db_free_result']($request);

	$smcFunc['db_insert']('replace',
		'{db_prefix}sp_profiles',
		array('id_profile' => 'int', 'type' => 'int', 'name' => 'text', 'value' => 'text'),
		array(
			array(1, 1, '$_guests', '-1|'),
			array(2, 1, '$_members', implode(',', $post_groups) . ',0|'),
			array(3, 1, '$_everyone', implode(',', $post_groups) . ',0,-1|'),
		),
		array('id_profile')
	);
}

$result = $smcFunc['db_query']('','
	SELECT id_profile
	FROM {db_prefix}sp_profiles
	WHERE type = {int:type}
	LIMIT {int:limit}',
	array(
		'type' => 2,
		'limit' => 1,
	)
);
list ($has_style_profiles) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($has_style_profiles))
{
	$smcFunc['db_insert']('replace',
		'{db_prefix}sp_profiles',
		array('id_profile' => 'int', 'type' => 'int', 'name' => 'text', 'value' => 'text'),
		array(
			array(4, 2, '$_default_title_default_body', 'title_default_class~catbg|title_custom_class~|title_custom_style~|body_default_class~windowbg|body_custom_class~|body_custom_style~|no_title~|no_body~'),
			array(5, 2, '$_default_title_alternate_body', 'title_default_class~catbg|title_custom_class~|title_custom_style~|body_default_class~windowbg2|body_custom_class~|body_custom_style~|no_title~|no_body~'),
			array(6, 2, '$_alternate_title_default_body', 'title_default_class~titlebg|title_custom_class~|title_custom_style~|body_default_class~windowbg|body_custom_class~|body_custom_style~|no_title~|no_body~'),
			array(7, 2, '$_alternate_title_alternate_body', 'title_default_class~titlebg|title_custom_class~|title_custom_style~|body_default_class~windowbg2|body_custom_class~|body_custom_style~|no_title~|no_body~'),
			array(8, 2, '$_no_title_default_body', 'title_default_class~|title_custom_class~|title_custom_style~|body_default_class~windowbg|body_custom_class~|body_custom_style~|no_title~1|no_body~'),
			array(9, 2, '$_no_title_alternate_body', 'title_default_class~|title_custom_class~|title_custom_style~|body_default_class~windowbg2|body_custom_class~|body_custom_style~|no_title~1|no_body~'),
			array(10, 2, '$_default_title_roundframe', 'title_default_class~catbg|title_custom_class~|title_custom_style~|body_default_class~roundframe|body_custom_class~|body_custom_style~|no_title~|no_body~'),
			array(11, 2, '$_alternate_title_roundframe', 'title_default_class~titlebg|title_custom_class~|title_custom_style~|body_default_class~roundframe|body_custom_class~|body_custom_style~|no_title~|no_body~'),
			array(12, 2, '$_no_title_roundframe', 'title_default_class~|title_custom_class~|title_custom_style~|body_default_class~roundframe|body_custom_class~|body_custom_style~|no_title~1|no_body~'),
			array(13, 2, '$_no_title_information', 'title_default_class~|title_custom_class~|title_custom_style~|body_default_class~information|body_custom_class~|body_custom_style~|no_title~1|no_body~'),
		),
		array('id_profile')
	);
}

$result = $smcFunc['db_query']('','
	SELECT id_profile
	FROM {db_prefix}sp_profiles
	WHERE type = {int:type}
	LIMIT {int:limit}',
	array(
		'type' => 3,
		'limit' => 1,
	)
);
list ($has_visibility_profiles) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($has_visibility_profiles))
{
	$smcFunc['db_insert']('replace',
		'{db_prefix}sp_profiles',
		array('id_profile' => 'int', 'type' => 'int', 'name' => 'text', 'value' => 'text'),
		array(
			array(14, 3, '$_show_on_portal', 'portal|'),
			array(15, 3, '$_show_on_board_index', 'forum|'),
			array(16, 3, '$_show_on_all_actions', '|allaction'),
			array(17, 3, '$_show_on_all_boards', '|allboard'),
			array(18, 3, '$_show_on_all_pages', '|allpage'),
			array(19, 3, '$_show_on_all_categories', '|allcategory'),
			array(20, 3, '$_show_on_all_articles', '|allarticle'),
			array(21, 3, '$_show_everywhere', '|all'),
		),
		array('id_profile')
	);
}

$db_package_log = array();
foreach ($tables as $table_name => $null)
	$db_package_log[] = array('remove_table', $db_prefix . $table_name);

if (SMF == 'SSI')
	echo 'Database changes were carried out successfully.';