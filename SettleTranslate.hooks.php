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

	public static function onsfHTMLBeforeForm( &$targetTitle, &$html )
	{
		global $wgRequest, $wgOut;
		if( $wgRequest->getCheck('translateMode') && $wgRequest->getCheck('translateFrom') ) {
			$lang = $wgRequest->getVal('translateFrom');
			$wgOut->addModules('ext.settletranslate.badge');
			$html .= '<div class="alert alert-warning" role="alert">'.wfMessage( 'settle-translate-form-warning', Language::fetchLanguageName( $lang ) )->plain().'</div>';
		}
	}

}
