<?php

/**
 * Admin
 */
define('DURA_ADMIN_NAME', 'admin');
define('DURA_ADMIN_PASS', 'admin');

/**
 * URL & Path
 */
define('DURA_URL', 'http://drrr.cf'); // DO NOT ADD SLASH TO END.
define('DURA_PATH', dirname(__FILE__));

/**
 * Trust Path directory sould be put outside of Document Root.
 */
define('DURA_TRUST_PATH', DURA_PATH.'/trust_path');
define('DURA_XML_PATH', DURA_TRUST_PATH.'/xml');
define('DURA_TEMPLATE_PATH', DURA_TRUST_PATH.'/template');

/**
 * If use mod_rewrite, set true.
 */
define('DURA_USE_REWRITE', false);

/**
 * Chat room settings
 */
define('DURA_LOG_LIMIT', 25);
define('DURA_TIMEOUT', 300); // 5 mins
define('DURA_USER_MIN', 40);
define('DURA_USER_MAX', 50);
define('DURA_ROOM_LIMIT', 10);
define('DURA_SITE_USER_CAPACITY', 150);
define('DURA_CHAT_ROOM_EXPIRE', 1800); // 30 mins
define('DURA_MESSAGE_MAX_LENGTH', 79);

/**
 * Language setting
 */
define('DURA_LANGUAGE', 'zh-CN');

/**
 * Title settings
 */
define('DURA_TITLE', 'Durarara like chat room');
define('DURA_SUBTITLE', 'Durarara fan community');

/**
 * Session name
 */
define('DURA_SESSION_NAME', 'durarara-like-chat');

/**
 * Comet settings
 */
define('DURA_USE_COMET', 0);
define('DURA_SLEEP_LOOP', 300);
define('DURA_SLEEP_TIME', 1);

?>