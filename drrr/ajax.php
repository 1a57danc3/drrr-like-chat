<?php
/**
 * A simple description for this script
 *
 * PHP Version 5.2.0 or Upper version
 *
 * @package    Dura
 * @author     Hidehito NOZAWA aka Suin <http://suin.asia>
 * @copyright  2010 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3
 *
 */

if ( file_exists('setting.php') )
{
	require 'setting.php';
}
else
{
	require 'setting.dist.php';
}

require 'dura.php';

Dura::setup();

if ( !isset($_SESSION['room']['id']) )
{
	// Session not exists.
	header('Content-Type: application/xml; charset=UTF-8');
	die('<?xml version="1.0" encoding="UTF-8"?><room><error>1</error></room>');
}

$id = $_SESSION['room']['id'];

$roomHandler = new Dura_Model_RoomHandler;
$roomModel   = $roomHandler->load($id);

if ( !$roomModel )
{
	// Room not found.
	header('Content-Type: application/xml; charset=UTF-8');
	die('<?xml version="1.0" encoding="UTF-8"?><room><error>2</error></room>');
}

$file = $roomHandler->getFilePath($id);

$content = md5(file_get_contents($file));

session_write_close();

if ( !isset($_GET['fast']) )
{
	for ( $i = 0; $i < DURA_SLEEP_LOOP; $i++ )
	{
		if ( $content != md5(file_get_contents($file)) )
		{
			break;
		}
	
		sleep(DURA_SLEEP_TIME);

		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false ) // TODO
		{
			break;
		}
	}
}

$roomModel = $roomHandler->load($id);

$userId = $_SESSION['user']->getId();
$isLogin = false;

foreach ( $roomModel->users as $user )
{
	if ( $userId == (string) $user->id )
	{
		$isLogin = true;
	}
}

if ( !$isLogin )
{
	session_name(DURA_SESSION_NAME);
	session_start();
	unset($_SESSION['room']);
	// Room timeout.
	header('Content-Type: application/xml; charset=UTF-8');
	die('<?xml version="1.0" encoding="UTF-8"?><room><error>3</error></room>');
}

$roomModel->addChild('error', 0);

foreach ( $roomModel->talks as $talk )
{
	if ( (string) $talk->uid == 0 )
	{
		$name    = (string) $talk->name;
		$message = (string) $talk->message;

		$talk->message = t($message, $name);
	}
}

header('Content-Type: application/xml; charset=UTF-8');
die($roomModel->asXML());

?>
