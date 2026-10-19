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

namespace lenu\postreactions;

class ext extends \phpbb\extension\base
{
	function enable_step($old_state)
	{
		if ($old_state === '')
		{
			$this->container->get('notification_manager')->enable_notifications('lenu.postreactions.notification.type.reaction');
			return 'notifications';
		}

		return parent::enable_step($old_state);
	}

	function disable_step($old_state)
	{
		if ($old_state === '')
		{
			$this->container->get('notification_manager')->disable_notifications('lenu.postreactions.notification.type.reaction');
			return 'notifications';
		}

		return parent::disable_step($old_state);
	}

	function purge_step($old_state)
	{
		if ($old_state === '')
		{
			$this->container->get('notification_manager')->purge_notifications('lenu.postreactions.notification.type.reaction');
			return 'notifications';
		}

		return parent::purge_step($old_state);
	}
}
