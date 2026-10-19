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

namespace lenu\postreactions\notification;

class reaction extends \phpbb\notification\type\base
{
	protected $language_key = 'NOTIFICATION_POST_REACTION';

	static public $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_POST_REACTION',
		'group'	=> 'NOTIFICATION_GROUP_POSTING',
	);

	protected $user_loader;

	public function get_type()
	{
		return 'lenu.postreactions.notification.type.reaction';
	}

	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	static public function get_item_id($reaction_data)
	{
		return (int) $reaction_data['post_id'];
	}

	static public function get_item_parent_id($reaction_data)
	{
		return (int) $reaction_data['topic_id'];
	}

	public function find_users_for_notification($reaction_data, $options = array())
	{
		return $this->check_user_notification_options(
			array((int) $reaction_data['poster_id']),
			$options
		);
	}

	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('actor_id'));
	}

	public function users_to_query()
	{
		return array((int) $this->get_data('actor_id'));
	}

	public function get_title()
	{
		$actor_name = $this->user_loader->get_username($this->get_data('actor_id'), 'no_profile');
		return $this->language->lang(
			$this->language_key,
			$actor_name,
			$this->get_data('emoji')
		);
	}

	public function get_url()
	{
		$post_id = (int) $this->item_id;
		return append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 'p=' . $post_id . '#p' . $post_id);
	}

	public function get_reference()
	{
		return $this->language->lang(
			'NOTIFICATION_REFERENCE',
			censor_text($this->get_data('post_subject'))
		);
	}

	public function create_insert_array($reaction_data, $pre_create_data = array())
	{
		$this->set_data('actor_id', $reaction_data['actor_id']);
		$this->set_data('emoji', $reaction_data['emoji']);
		$this->set_data('post_subject', $reaction_data['post_subject']);
		parent::create_insert_array($reaction_data, $pre_create_data);
	}
}
