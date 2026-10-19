<?php
/**
*
* Post reactions extension for the phpBB Forum Software package.
*
* @package   lenu/postreactions
* @copyright (c) 2026 Lenu
* @license   GPL-2.0
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_POSTREACTIONS'						=> 'Post reactions',
	'ACP_POSTREACTIONS_SETTINGS'				=> 'Settings',
	'ACP_POSTREACTIONS_SETTINGS_EXPLAIN'		=> 'Configure the availability and guest visibility of post reactions.',
	'ACP_REACTIONS_ENABLED'					=> 'Enable post reactions',
	'ACP_REACTIONS_ENABLED_EXPLAIN'			=> 'Allow users to view and use post reactions.',
	'ACP_REACTIONS_GUESTS_READONLY'			=> 'Show reactions to guests in read-only mode',
	'ACP_REACTIONS_GUESTS_READONLY_EXPLAIN'	=> 'Guests can see existing reactions and counts, but cannot react. Disable this to hide reactions from guests.',
	'ACP_REACTIONS_NOTIFICATIONS_ENABLED'		=> 'Notify post authors about new reactions',
	'ACP_REACTIONS_NOTIFICATIONS_ENABLED_EXPLAIN'	=> 'Send a phpBB notification to the post author when another user reacts to their post.',
));
