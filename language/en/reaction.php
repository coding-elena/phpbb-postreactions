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
	'ADD_REACTION' => 'Add reaction',
	'INCORRECT_REACTION' => 'Invalid reaction.',
	'NOTIFICATION_POST_REACTION' => '%1$s reacted to your post with %2$s',
	'NOTIFICATION_TYPE_POST_REACTION' => 'Someone reacted to your post',
	'REACTION_REQUEST_INVALID' => 'The reaction request could not be validated. Reload the page and try again.',
	'REACTION_UPDATE_ERROR' => 'The reaction could not be updated. Please try again.',
));
