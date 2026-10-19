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

class v_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'reactions') && $this->db_tools->sql_column_exists($this->table_prefix . 'reactions', 'poster_id');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		if (!$this->db_tools->sql_table_exists($this->table_prefix . 'reactions'))
		{
			return array(
				'add_tables' => array(
					$this->table_prefix . 'reactions' => array(
						'COLUMNS'		=> array(
							'post_id'		=> array('UINT', 0),
							'topic_id'		=> array('UINT', 0),
							'forum_id'		=> array('UINT', 0),
							'user_id'		=> array('UINT', 0),
							'poster_id'		=> array('UINT', 0),
							'reaction_time'	=> array('UINT:11', 0),							
							'reaction' => array('VCHAR', 4),
						),
						'PRIMARY_KEY'	=> array('post_id', 'user_id'),
					),
				),
			);
		}

		return array(
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('reactions_enabled', 1)),
			array('config.add', array('reactions_guests_readonly', 1)),
			array('config.add', array('reactions_notifications_enabled', 0)),
			array('permission.add', array('f_reactions', false)),
			array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_reactions', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_STANDARD', 'f_reactions', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_POLLS', 'f_reactions', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED', 'f_reactions', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED_POLLS', 'f_reactions', 'role', true)),						
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array($this->table_prefix . 'reactions'),
		);
	}

	public function revert_data()
	{
		return array(
		);
	}
}
