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

namespace lenu\postreactions\migrations;

class v_1_0_3 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\lenu\postreactions\migrations\v_1_0_2');
	}

	public function update_data()
	{
		return array(
			array('module.remove', array(
				'acp',
				'ACP_POSTREACTIONS',
				'ACP_POSTREACTIONS_SETTINGS',
			)),
			array('module.add', array(
				'acp',
				'ACP_POSTREACTIONS',
				array(
					'module_basename' => '\lenu\postreactions\acp\main_module',
					'modes' => array('settings'),
				),
			)),
		);
	}
}
