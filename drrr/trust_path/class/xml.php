<?php


class Dura_Class_Xml extends SimpleXMLElement
{
	public function asXML()
	{
		$string = parent::asXML();
		$this->_creanupXML($string);
		return $string;
	}

	public function asArray()
	{
		$this->_objectToArray($this);
		return $this;
	}

	protected function _creanupXML(&$string)
	{
		$string = preg_replace("/>\s*</", ">\n<", $string);
		$lines  = explode("\n", $string);
		$string = array_shift($lines) . "\n";
		$depth  = 0;

		foreach ( $lines as $line )
		{
			if ( preg_match('/^<[\w]+>$/U', $line) )
			{
				$string .= str_repeat("\t", $depth);
				$depth++;
			}
			elseif ( preg_match('/^<\/.+>$/', $line) )
			{
				$depth--;
				$string .= str_repeat("\t", $depth);
			}
			else
			{
				$string .= str_repeat("\t", $depth);
			}

			$string .= $line . "\n";
		}

		$string = trim($string);
	}

	protected function _objectToArray(&$object)
	{
		if ( is_object($object) ) $object = (array) $object;
		if ( !is_array($object) ) return;

		foreach ( $object as &$member )
		{
			$this->_objectToArray($member);
		}
	}
}

?>
