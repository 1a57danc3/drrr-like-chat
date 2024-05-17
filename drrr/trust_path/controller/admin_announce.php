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

class Dura_Controller_AdminAnnounce extends Dura_Abstract_Controller
{
	protected $roomHandler = null;
	protected $roomModel   = null;

	public function __construct()
	{
		parent::__construct();

		$this->_validateAdmin();

		$this->roomHandler = new Dura_Model_RoomHandler;
		$this->roomModels = $this->roomHandler->loadAll();
	}

	public function main()
	{
		if ( Dura::post('message') )
		{
			$this->_message();
		}

		$this->_default();
	}

	protected function _message()
	{
		$message = Dura::post('message');
		$message = trim($message);
		$messageId = md5(microtime().mt_rand());

		if ( !$message ) return;

		foreach ( $this->roomModels as $roomId => $roomModel )
		{
			$talk = $roomModel->addChild('talks');
			$talk->addChild('id', $messageId);
			$talk->addChild('uid', Dura::user()->getId());
			$talk->addChild('name', Dura::user()->getName());
			$talk->addChild('message', $message);
			$talk->addChild('icon', Dura::user()->getIcon());
			$talk->addChild('time', time());

			$id = Dura::user()->getId();

			foreach ( $roomModel->users as $user )
			{
				if ( $id == (string) $user->id )
				{
					$user->update = time();
				}
			}

			while ( count($roomModel->talks) > DURA_LOG_LIMIT )
			{
				unset($roomModel->talks[0]);
			}

			$this->roomHandler->save($roomId, $roomModel);
		}

		Dura::redirect('admin_announce');
	}

	protected function _default()
	{
		$talks = array();
		$userId = Dura::user()->getId();

		foreach ( $this->roomModels as $roomModel )
		{
			foreach ( $roomModel->talks as $talk )
			{
				$time = (int) $talk->time;
				$id   = (string) $talk->id;

				if ( isset($talks[$time][$id]) ) continue;

				$talks[$time][$id] = (array) $talk;
			}
		}

		ksort($talks);

		$talks = array_reverse($talks);

		$this->output['talks'] = $talks;

		$this->_view();
	}
}

?>
