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

namespace lenu\postreactions\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $template;
	protected $user;
	protected $request;
	protected $controller_helper;
	protected $phpbb_root_path;
	protected $php_ext;
	protected $helper;

	public function __construct(\phpbb\template\template $template, \phpbb\user $user, \phpbb\request\request_interface $request, \phpbb\controller\helper $controller_helper, $phpbb_root_path, $php_ext, \lenu\postreactions\core\helper $helper)
	{
		$this->template = $template;
		$this->user = $user;
		$this->request = $request;
		$this->controller_helper = $controller_helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->helper = $helper;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.delete_posts_in_transaction'			=> 'delete_post_reactions',
			'core.viewtopic_get_post_data'				=> 'load_post_reactions',
			'core.viewtopic_modify_post_row'			=> 'assign_post_reactions',
			'core.user_setup'							=> 'load_language_on_setup',
			'core.permissions'							=> 'add_permission',
		);
	}

	public function delete_post_reactions($event)
	{
		$this->helper->delete_post_reactions($event['post_ids']);
	}

	public function load_post_reactions($event)
	{
		$post_list = $event['post_list'];
		$forum_id = (int) $event['forum_id'];

		if ($this->request->is_set('react'))
		{
			$post_id = $this->request->variable('react', 0);
			$reaction_key = $this->request->variable('reaction', '');
			if (!$this->helper->set_reaction($post_id, $this->user->data['user_id'], $reaction_key))
			{
				trigger_error($this->user->lang['INCORRECT_REACTION']);
			}

			redirect(append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", 'f=' . $forum_id . '&amp;p=' . $post_id . '#p' . $post_id));
		}

		$this->helper->load_post_reactions($post_list, $this->user->data['user_id']);
	}

	public function assign_post_reactions($event)
	{
		$row = $event['row'];
		$post_row = $event['post_row'];
		$forum_id = (int) $row['forum_id'];
		$poster_id = (int) $row['user_id'];
		$topic_id = (int) $row['topic_id'];

		$post_row['POST_REACTIONS'] = $this->helper->get_post_reactions(
			$row['post_id'],
			$this->user->data['user_id'],
			$forum_id,
			$poster_id
		);
		$post_row['POST_REACTION_OPTIONS'] = $this->helper->get_reaction_options(
			$row['post_id'],
			$this->user->data['user_id'],
			$forum_id,
			$poster_id,
			$topic_id
		);
		$post_row['U_REACTION_AJAX'] = $this->controller_helper->route('lenu_postreactions_reaction_controller', array(
			'post_id' => (int) $row['post_id'],
		));
		$post_row['REACTION_HASH'] = generate_link_hash('post_reaction_' . (int) $row['post_id']);
		$event['post_row'] = $post_row;
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'lenu/postreactions',
			'lang_set' => 'reaction',
		);
		$lang_set_ext[] = array(
			'ext_name' => 'lenu/postreactions',
			'lang_set' => 'permissions',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['f_reactions'] = array('lang' => 'ACL_F_REACTIONS', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}
}
