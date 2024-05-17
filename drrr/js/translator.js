var Translator = function()
{
	this.catalog = {};
	
	this.translate = function(message)
	{
		try
		{
			if ( Translator.catalog[message] )
			{
				return Translator.catalog[message];
			}
		}
		catch(e)
		{
		}

		return message;
	};
	
	return this;
}

translator = new Translator();

function t(message)
{
	return translator.translate(message);
}
