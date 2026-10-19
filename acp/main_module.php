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

class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $config, $request, $user, $template, $phpbb_container;

		$user->add_lang_ext('lenu/postreactions', 'info_acp_postreactions');
		$form_key = 'acp_postreactions';
		add_form_key($form_key);
		$submit = $request->is_set_post('submit');
		$display_vars = array(
			'title' => 'ACP_POSTREACTIONS_SETTINGS',
			'vars' => array(
				'legend' => 'GENERAL_OPTIONS',
				'reactions_enabled' => array(
					'lang' => 'ACP_REACTIONS_ENABLED',
					'validate' => 'bool',
					'type' => 'radio:yes_no',
					'explain' => true,
				),
				'reactions_guests_readonly' => array(
					'lang' => 'ACP_REACTIONS_GUESTS_READONLY',
					'validate' => 'bool',
					'type' => 'radio:yes_no',
					'explain' => true,
				),
				'reactions_notifications_enabled' => array(
					'lang' => 'ACP_REACTIONS_NOTIFICATIONS_ENABLED',
					'validate' => 'bool',
					'type' => 'radio:yes_no',
					'explain' => true,
				),
			),
		);

		$new_config = $config;
		$cfg_array = $request->is_set('config') ? $request->variable('config', array('' => ''), true) : $new_config;
		$error = array();
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang('FORM_INVALID');
		}

		if (sizeof($error))
		{
			$submit = false;
		}

		foreach ($display_vars['vars'] as $config_name => $vars)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$new_config[$config_name] = $config_value = $cfg_array[$config_name];
			if ($submit)
			{
				$config->set($config_name, $config_value);
			}
		}

		if ($submit)
		{
			$phpbb_log = $phpbb_container->get('log');
			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_CONFIG_' . strtoupper($mode));
			trigger_error($user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_postreactions';
		$this->page_title = $display_vars['title'];
		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang($display_vars['title']),
			'L_TITLE_EXPLAIN'	=> $user->lang($display_vars['title'] . '_EXPLAIN'),
			'S_ERROR'			=> (bool) sizeof($error),
			'ERROR_MSG'			=> implode('<br />', $error),
			'U_ACTION'			=> $this->u_action,
		));

		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND' => true,
					'LEGEND' => $user->lang($vars),
				));
				continue;
			}

			$type = explode(':', $vars['type']);
			$content = build_cfg_template($type, $config_key, $new_config, $config_key, $vars);
			$template->assign_block_vars('options', array(
				'KEY' => $config_key,
				'TITLE' => $user->lang($vars['lang']),
				'S_EXPLAIN' => $vars['explain'],
				'TITLE_EXPLAIN' => $user->lang($vars['lang'] . '_EXPLAIN'),
				'CONTENT' => $content,
			));
		}
	}
}
