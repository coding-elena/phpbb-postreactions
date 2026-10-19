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
  'ACP_POSTREACTIONS'                   => 'Post reactions',
  'ACP_POSTREACTIONS_SETTINGS'          => 'Configurazione',
  'ACP_POSTREACTIONS_SETTINGS_EXPLAIN'  => 'Configura la disponibilità e la visibilità per gli ospiti delle reazioni ai messaggi.',
  'ACP_REACTIONS_ENABLED'               => 'Abilita reazioni nei post',
  'ACP_REACTIONS_ENABLED_EXPLAIN'       => 'Consente agli utenti di visualizzare e lasciare reazioni ai post.',
  'ACP_REACTIONS_GUESTS_READONLY'       => 'Mostra le reazioni agli ospiti in modalità sola lettura',
  'ACP_REACTIONS_GUESTS_READONLY_EXPLAIN' => 'Gli ospiti possono vedere le reazioni esistenti e i conteggi, ma non possono reagire. Disabilita questa opzione per nascondere le reazioni agli ospiti.',
  'ACP_REACTIONS_NOTIFICATIONS_ENABLED'   => 'Notifica agli autori dei post le nuove reazioni',
  'ACP_REACTIONS_NOTIFICATIONS_ENABLED_EXPLAIN' => 'Invia una notifica di phpBB all’autore del post quando un altro utente ha lasciato una reazione.',
));
