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

namespace lenu\postreactions\acp;

class main_info
{
	public function module()
	{
		global $user;

		$user->add_lang_ext('lenu/postreactions', 'info_acp_postreactions');

		return array(
			'filename'	=> '\lenu\postreactions\acp\main_module',
			'title'		=> 'ACP_POSTREACTIONS_SETTINGS',
			'modes'		=> array(
				'settings' => array(
					'title'	=> 'ACP_POSTREACTIONS_SETTINGS',
					'auth'	=> 'ext_lenu/postreactions && acl_a_board',
					'cat'	=> array('ACP_POSTREACTIONS'),
				),
			),
		);
	}
}
