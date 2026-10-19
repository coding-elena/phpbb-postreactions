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

namespace lenu\postreactions\controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class reaction
{
	protected $request;
	protected $user;
	protected $helper;

	public function __construct(\phpbb\request\request_interface $request, \phpbb\user $user, \lenu\postreactions\core\helper $helper)
	{
		$this->request = $request;
		$this->user = $user;
		$this->helper = $helper;
	}

	public function main($post_id)
	{
		$post_id = (int) $post_id;
		if (!$this->request->is_set_post('reaction'))
		{
			return new JsonResponse(array('error' => $this->user->lang['REACTION_REQUEST_INVALID']), 405);
		}

		$hash = $this->request->variable('hash', '');
		if (!check_link_hash($hash, 'post_reaction_' . $post_id))
		{
			return new JsonResponse(array('error' => $this->user->lang['REACTION_REQUEST_INVALID']), 403);
		}

		$reaction_key = $this->request->variable('reaction', '');
		if (!$this->helper->set_reaction($post_id, $this->user->data['user_id'], $reaction_key))
		{
			return new JsonResponse(array('error' => $this->user->lang['INCORRECT_REACTION']), 403);
		}

		$reactions = $this->helper->get_post_reaction_state($post_id, $this->user->data['user_id']);
		if ($reactions === false)
		{
			return new JsonResponse(array('error' => $this->user->lang['INCORRECT_REACTION']), 404);
		}

		return new JsonResponse(array('reactions' => $reactions));
	}
}
