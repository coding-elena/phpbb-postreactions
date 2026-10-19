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

namespace lenu\postreactions\core;

class helper
{
	protected $config;
	protected $db;
	protected $auth;
	protected $user;
	protected $phpbb_root_path;
	protected $php_ext;
	protected $reactions_table;
	protected $posts_table;
	protected $notification_manager;
	protected $post_reactions = array();
	protected $reaction_options = array(
		'like' => array('code' => '(y)', 'emoji' => '👍'),
		'love' => array('code' => '<3', 'emoji' => '❤️'),
		'laugh' => array('code' => 'XD', 'emoji' => '😂'),
		'wow' => array('code' => ':O', 'emoji' => '😮'),
		'sad' => array('code' => ':\'(', 'emoji' => '😢'),
		'angry' => array('code' => '>:(', 'emoji' => '😡'),
	);

	protected $topic_cache = array();

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\user $user, $phpbb_root_path, $php_ext, $reactions_table, $posts_table, \phpbb\notification\manager $notification_manager)
	{
		$this->config = $config;
		$this->db = $db;
		$this->auth = $auth;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->reactions_table = $reactions_table;
		$this->posts_table = $posts_table;
		$this->notification_manager = $notification_manager;
	}

	public function can_react_to_post($user_id, $poster_id, $forum_id, $post_id, $topic_id = null)
	{
		$canreact = !empty($this->config['reactions_enabled'])
			&& $user_id != ANONYMOUS
			&& $this->user->data['user_type'] != USER_IGNORE
			&& $user_id != $poster_id
			&& $this->auth->acl_get('f_reactions', $forum_id);

		if ($canreact) {
			$topic_data = $this->get_topic_data($post_id, $topic_id);
			if ($topic_data) {
				$topic_locked = isset($topic_data['topic_status']) && $topic_data['topic_status'] == ITEM_LOCKED;
				$forum_locked = isset($topic_data['forum_status']) && $topic_data['forum_status'] == ITEM_LOCKED;
				if ($topic_locked || $forum_locked) {
					$canreact = false;
				}
			}
		}

		return $canreact;
	}

	public function can_view_reactions($user_id)
	{
		return !empty($this->config['reactions_enabled'])
			&& ($user_id != ANONYMOUS || !empty($this->config['reactions_guests_readonly']));
	}

	public function set_reaction($post_id, $user_id, $reaction_key)
	{
		if (!isset($this->reaction_options[$reaction_key]) || $user_id == ANONYMOUS)
		{
			return false;
		}

		$sql = 'SELECT poster_id, topic_id, forum_id, post_subject
			FROM ' . $this->posts_table . '
			WHERE post_id = ' . (int) $post_id;
		$result = $this->db->sql_query($sql);
		$post = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$post || !$this->can_react_to_post($user_id, $post['poster_id'], $post['forum_id'], $post_id))
		{
			return false;
		}

		$sql = 'SELECT reaction
			FROM ' . $this->reactions_table . '
			WHERE post_id = ' . (int) $post_id . '
				AND user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$current_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($current_row && $this->get_reaction_key($current_row['reaction']) === $reaction_key)
		{
			$sql = 'DELETE FROM ' . $this->reactions_table . '
				WHERE post_id = ' . (int) $post_id . '
					AND user_id = ' . (int) $user_id;
			$this->db->sql_query($sql);
			return true;
		}

		$reaction_data = array(
			'user_id'		=> (int) $user_id,
			'post_id'		=> (int) $post_id,
			'poster_id'		=> (int) $post['poster_id'],
			'topic_id'		=> (int) $post['topic_id'],
			'forum_id'		=> (int) $post['forum_id'],
			'reaction'		=> $this->reaction_options[$reaction_key]['code'],
			'reaction_time'	=> time(),
		);

		if ($current_row)
		{
			$sql = 'UPDATE ' . $this->reactions_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $reaction_data) . '
				WHERE post_id = ' . (int) $post_id . '
					AND user_id = ' . (int) $user_id;
		}
		else
		{
			$sql = 'INSERT INTO ' . $this->reactions_table . ' ' . $this->db->sql_build_array('INSERT', $reaction_data);
		}
		$this->db->sql_query($sql);

		if (!$current_row && !empty($this->config['reactions_notifications_enabled']))
		{
			$this->notification_manager->add_notifications('lenu.postreactions.notification.type.reaction', array(
				'actor_id'		=> (int) $user_id,
				'post_id'		=> (int) $post_id,
				'poster_id'		=> (int) $post['poster_id'],
				'topic_id'		=> (int) $post['topic_id'],
				'forum_id'		=> (int) $post['forum_id'],
				'post_subject'	=> $post['post_subject'],
				'emoji'			=> $this->reaction_options[$reaction_key]['emoji'],
			));
		}

		return true;
	}

	public function load_post_reactions($post_ids, $user_id)
	{
		$this->post_reactions = array();
		if (empty($post_ids) || !$this->can_view_reactions($user_id))
		{
			return;
		}

		$sql = 'SELECT post_id, user_id, reaction
			FROM ' . $this->reactions_table . '
			WHERE ' . $this->db->sql_in_set('post_id', $post_ids);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$post_id = (int) $row['post_id'];
			$reaction_key = $this->get_reaction_key($row['reaction']);
			if (!isset($this->post_reactions[$post_id]))
			{
				$this->post_reactions[$post_id] = array(
					'counts' => array(),
					'selected' => '',
				);
			}

			if ($reaction_key !== false)
			{
				if (!isset($this->post_reactions[$post_id]['counts'][$reaction_key]))
				{
					$this->post_reactions[$post_id]['counts'][$reaction_key] = 0;
				}
				$this->post_reactions[$post_id]['counts'][$reaction_key]++;
			}

			if ($reaction_key !== false && (int) $row['user_id'] === (int) $user_id)
			{
				$this->post_reactions[$post_id]['selected'] = $reaction_key;
			}
		}
		$this->db->sql_freeresult($result);
	}

	public function get_post_reactions($post_id, $user_id, $forum_id, $poster_id)
	{
		if (!$this->can_view_reactions($user_id))
		{
			return array();
		}

		$post_reactions = isset($this->post_reactions[$post_id]) ? $this->post_reactions[$post_id] : array(
			'counts' => array(),
			'selected' => '',
		);
		$can_react = $this->can_react_to_post($user_id, $poster_id, $forum_id, $post_id);
		$reaction_rows = array();

		foreach ($this->reaction_options as $reaction_key => $reaction)
		{
			$reaction_count = isset($post_reactions['counts'][$reaction_key]) ? $post_reactions['counts'][$reaction_key] : 0;
			if (!$reaction_count)
			{
				continue;
			}

			$reaction_rows[] = array(
				'KEY'		=> $reaction_key,
				'EMOJI'		=> $reaction['emoji'],
				'COUNT'		=> $reaction_count,
				'SELECTED'	=> $post_reactions['selected'] === $reaction_key,
				'URL'		=> $can_react ? $this->get_reaction_url($post_id, $forum_id, $reaction_key) : '',
			);
		}

		return $reaction_rows;
	}

	public function get_reaction_options($post_id, $user_id, $forum_id, $poster_id, $topic_id)
	{
		if (!$this->can_react_to_post($user_id, $poster_id, $forum_id, $post_id, $topic_id))
		{
			return array();
		}

		$options = array();
		foreach ($this->reaction_options as $reaction_key => $reaction)
		{
			$options[] = array(
				'KEY'		=> $reaction_key,
				'EMOJI'		=> $reaction['emoji'],
				'SELECTED'	=> isset($this->post_reactions[$post_id]['selected']) && $this->post_reactions[$post_id]['selected'] === $reaction_key,
				'URL'		=> $this->get_reaction_url($post_id, $forum_id, $reaction_key),
			);
		}

		return $options;
	}

	public function get_post_reaction_state($post_id, $user_id)
	{
		$sql = 'SELECT poster_id, forum_id
			FROM ' . $this->posts_table . '
			WHERE post_id = ' . (int) $post_id;
		$result = $this->db->sql_query($sql);
		$post = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$post || !$this->can_view_reactions($user_id))
		{
			return false;
		}

		$this->load_post_reactions(array((int) $post_id), $user_id);
		$post_reactions = isset($this->post_reactions[$post_id]) ? $this->post_reactions[$post_id] : array(
			'counts' => array(),
			'selected' => '',
		);
		$reactions = array();

		foreach ($this->reaction_options as $reaction_key => $reaction)
		{
			$count = isset($post_reactions['counts'][$reaction_key]) ? $post_reactions['counts'][$reaction_key] : 0;
			if ($count)
			{
				$reactions[] = array(
					'key'		=> $reaction_key,
					'emoji'		=> $reaction['emoji'],
					'count'		=> $count,
					'selected'	=> $post_reactions['selected'] === $reaction_key,
				);
			}
		}

		return $reactions;
	}

	public function delete_post_reactions($post_ids)
	{
		if (empty($post_ids))
		{
			return;
		}

		$sql = 'DELETE FROM ' . $this->reactions_table . '
			WHERE ' . $this->db->sql_in_set('post_id', $post_ids);
		$this->db->sql_query($sql);
	}

	protected function get_reaction_key($reaction)
	{
		foreach ($this->reaction_options as $reaction_key => $reaction_data)
		{
			if ($reaction === $reaction_data['code'] || $reaction === $reaction_data['emoji'])
			{
				return $reaction_key;
			}
		}

		return false;
	}

	protected function get_reaction_url($post_id, $forum_id, $reaction_key)
	{
		return append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", 'f=' . (int) $forum_id . '&amp;p=' . (int) $post_id . '&amp;react=' . (int) $post_id . '&amp;reaction=' . $reaction_key . '#p' . (int) $post_id);
	}

	/**
	 * Fetches all topic solved data related to the given post.
	 *
	 * @param int $post_id ID of post to fetch topic/forum data for.
	 *
	 * @return mixed topic data, or false if not found
	 */
	public function get_topic_data($post_id, $topic_id = null)
	{
		if ($topic_id !== null)
		{
			if (isset($this->topic_cache[$topic_id]))
			{
				return $this->topic_cache[$topic_id];
			}
		}

		$select_sql_array = array(
			'SELECT' =>
				't.topic_id, t.topic_poster, t.topic_status, t.topic_type, ' .
				'f.forum_id, p.post_id ',
			'FROM' => array(
				FORUMS_TABLE => 'f',
				POSTS_TABLE => 'p',
				TOPICS_TABLE => 't',
			),
			'WHERE' =>
				'p.post_id = ' . (int) $post_id .
				' AND t.topic_id = p.topic_id AND f.forum_id = t.forum_id',
		);
		$select_sql = $this->db->sql_build_query('SELECT', $select_sql_array);
		$result = $this->db->sql_query($select_sql);
		$topic_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($topic_data)
		{
			$this->topic_cache[$topic_data['topic_id']] = $topic_data;
		}

		return $topic_data;
	}

}
