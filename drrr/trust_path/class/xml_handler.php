<?php

class Dura_Class_XmlHandler
{
	protected $errors    = array();
	protected $className = 'Dura_Class_Xml';
	protected $fileName  = 'xml';

	public function __construct($className = null)
	{
		if ( $className )
		{
			$this->className = $className;
		}
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function create()
	{
		$string = $this->_getDefaultXml();
		$xml = simplexml_load_string($string, $this->className);
		return $xml;
	}

	public function load($id)
	{
		$file = $this->getFilePath($id);

		libxml_use_internal_errors(true);
		$xml = simplexml_load_file($file, $this->className, LIBXML_NOCDATA);

		if ( !$xml )
		{
			$error = array();
			$error['file']    = $file;
			$error['message'] = '';

			foreach ( libxml_get_errors() as $xmlError )
			{
				$error['message'] .= $xmlError->message;
			}

			$this->errors[] = $error;

			// TODO >> Error Logger

			return false;
		}

		return $xml;
	}

	public function save($id, $xml)
	{
		$xml->update = time();
		$file = $this->getFilePath($id);
		return file_put_contents($file, $xml->asXML(), LOCK_EX);
	}

	public function delete($id)
	{
		$file = $this->getFilePath($id);
		return @unlink($file);
	}

	public function getFilePath($id)
	{
		return DURA_XML_PATH.'/'.$this->fileName.'_'.$id.'.xml';
	}

	protected function _getDefaultXml()
	{
		return 
		'<?xml version="1.0" encoding="UTF-8"?> 
		<root>
		</root>';
	}
}

?>
