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

class v_1_0_1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\lenu\postreactions\migrations\v_1_0_0');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_POSTREACTIONS',
			)),
			array('module.add', array(
				'acp',
				'ACP_POSTREACTIONS',
				array(
					'module_basename' => '\lenu\postreactions\acp\acp_postreactions_module',
					'modes' => array('settings'),
				),
			)),
		);
	}

	public function revert_data()
	{
		return array(
			array('module.remove', array(
				'acp',
				'ACP_POSTREACTIONS',
				array(
					'module_basename' => '\lenu\postreactions\acp\acp_postreactions_module',
					'modes' => array('settings'),
				),
			)),
			array('module.remove', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_POSTREACTIONS',
			)),
		);
	}
}
