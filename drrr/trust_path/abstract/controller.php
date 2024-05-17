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

abstract class Dura_Abstract_Controller
{
	protected $output   = array();
	protected $template = null;

	public function __construct()
	{
	}

	public function main()
	{
	}

	protected function _view()
	{
		if ( !$this->template )
		{
			$this->template = DURA_TEMPLATE_PATH.'/'.Dura::$controller.'.'.Dura::$action.'.php';
		}

		$this->_escapeHtml($this->output);

		ob_start();
		$this->_display($this->output);
		$content = ob_get_contents();
		ob_end_clean();

		$this->_render($content);
	}

	protected function _display($dura)
	{
		require $this->template;
	}

	protected function _render($content)
	{
		require DURA_TEMPLATE_PATH.'/theme.php';
	}

	protected function _validateUser()
	{
		if ( !Dura::user()->isUser() )
		{
			Dura::redirect();
		}
	}

	protected function _validateAdmin()
	{
		if ( !Dura::user()->isAdmin() )
		{
			Dura::redirect();
		}
	}

	protected function _escapeHtml(&$vars)
	{
		foreach ( $vars as $key => &$var )
		{
			if ( is_array($var) )
			{
				$this->_escapeHtml($var);
			}
			elseif ( !is_object($var) )
			{
				$var = Dura::escapeHtml($var);
			}
		}
	}
}

?>
