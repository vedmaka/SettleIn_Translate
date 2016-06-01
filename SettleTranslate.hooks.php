<?php

/**
 * Hooks for SettleTranslate extension
 *
 * @file
 * @ingroup Extensions
 */
class SettleTranslateHooks
{

	public static function onExtensionLoad()
	{
		
	}

	/**
	 * @param Parser $parser
	 */
	public static function onParserFirstCallInit( $parser )
	{
		$parser->setFunctionHook('translate_link', 'SettleTranslate::renderLink', SFH_OBJECT_ARGS);
	}

}
