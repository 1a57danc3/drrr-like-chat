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

class Dura_Controller_Default extends Dura_Abstract_Controller
{
	protected $error = null;
	protected $icons = array();

	public function __construct()
	{
		parent::__construct();
		$this->icons = Dura_Class_Icon::getIcons();

		unset($this->icons['admin']);
	}

	public function main()
	{
		if ( Dura::user()->isUser() )
		{
			Dura::redirect('lounge');
		}

		if ( Dura::post('name') )
		{
			try
			{
				$this->_login();
			}
			catch ( Exception $e )
			{
				$this->error = $e->getMessage();
			}
		}

		$this->_default();
	}

	protected function _login()
	{
		$name = Dura::post('name');
		$icon = Dura::post('icon');
		$language = Dura::post('language');
		$name = trim($name);
		$icon = trim($icon);
		$language = trim($language);

		if ( $name === '' )
		{
			throw new Exception(t("Please input name."));
		}

		if ( mb_strlen($name) > 10 )
		{
			throw new Exception(t("Name should be less than 10 letters."));
		}

		$token = Dura::post('token');

		if ( !Dura_Class_Ticket::check($token) )
		{
			throw new Exception(t("Login error happened."));
		}

		if ( !isset($this->icons[$icon]) )
		{
			$icons = array_keys($this->icons);
			$icon = reset($icons);
		}

		$user =& Dura_Class_User::getInstance();
		$user->login($name, $icon, $language);

		Dura_Class_Ticket::destory();

		Dura::redirect('lounge');
	}

	protected function _default()
	{
		require_once DURA_TRUST_PATH.'/language/list.php';

		$languages = dura_get_language_list();

		foreach ( $languages as $langcode => $name )
		{
			if ( !file_exists(DURA_TRUST_PATH.'/language/'.$langcode.'.php') )
			{
				unset($languages[$langcode]);
			}
		}

		$acceptLangs = getenv('HTTP_ACCEPT_LANGUAGE');
		$acceptLangs = explode(',', $acceptLangs);
		$defaultLanguage = DURA_LANGUAGE;

		foreach ( $acceptLangs as $k => $acceptLang )
		{
			@list($langcode, $dummy) = explode(';', $acceptLang);

			foreach ( $languages as $language => $v )
			{
				if ( stripos($language, $langcode) === 0 )
				{
					$defaultLanguage = $language;
					break 2;
				}
			}
		}

		$this->output['languages'] = $languages;
		$this->output['default_language'] = $defaultLanguage;
		$this->output['icons'] = $this->icons;
		$this->output['error'] = $this->error;
		$this->output['token'] = Dura_Class_Ticket::issue();
		$this->_view();
	}
}

?>
