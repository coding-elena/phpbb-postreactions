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

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
  'ADD_REACTION' => 'Lascia una reazione',
  'INCORRECT_REACTION' => 'Reazione non valida.',
  'NOTIFICATION_POST_REACTION' => '%1$s ha lasciato una reazione al tuo post con %2$s',
  'NOTIFICATION_TYPE_POST_REACTION' => 'Qualcuno ha lasciato una reazione al tuo post',
  'REACTION_REQUEST_INVALID' => 'Impossibile recepire la tua reazione. Ricarica la pagina e riprova.',
  'REACTION_UPDATE_ERROR' => 'Impossibile aggiornare la tua reazione. Riprova.',
));
