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

class Dura_Class_RoomSession
{
	public static function isCreated()
	{
		return isset($_SESSION['room']);
	}

	public static function get($var = null)
	{
		if ( $var )
		{
			return $_SESSION['room'][$var];
		}

		return $_SESSION['room'];
	}

	public static function create($id)
	{
		$_SESSION['room']['id'] = $id;
	}

	public static function delete()
	{
		unset($_SESSION['room']);
	}
}

?>
