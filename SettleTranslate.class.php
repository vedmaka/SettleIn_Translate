<?php

/**
 * Class for SettleTranslate extension
 *
 * @file
 * @ingroup Extensions
 */
class SettleTranslate {

	/**
	 * @param Parser $parser
	 *
	 * @return string
	 */
	public static function renderLink( $parser ) {

		global $wgSettleTranslateDomains;

		$html = '';

		$title = $parser->getTitle();

		$parser->getOutput()->addModules( 'ext.settletranslate.window' );

		$html .= $parser->insertStripItem(
			'<a id="translate-link" href="#" type="button" class="btn btn-primary" data-toggle="modal" data-target=".translate-window"><i class="fa fa-language" ></i> '
			. wfMessage( 'settle-translate-link-text' )->plain() . '</a>' );

		$html .= '<div id="translate-window" class="modal fade bs-example-modal-sm translate-window" role="dialog">';
		$html .= '<div class="modal-dialog modal-sm">';
		$html .= '<div class="modal-content">';
		$html .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title" id="mySmallModalLabel">' . wfMessage( 'settle-translate-modal-title' ) . '</h4></div>';
		$html .= '<div class="modal-body">';
		$html .= '<p>' . wfMessage( 'settle-translate-intro-text' )->plain() . '</p>';
		$select = '<select class="form-control">';
		foreach ( $wgSettleTranslateDomains as $lang => $domain ) {
			$langText = Language::fetchLanguageName( $lang );
			if ( ! $langText ) {
				continue;
			}
			$select .= '<option value="' . self::generateDomainLink( $parser, 'Card', $lang ) . '">' . $langText . '</option>';
		}
		$select .= '</select>';
		$html .= $parser->insertStripItem( $select );
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div class="modal-footer">';
		$html .= $parser->insertStripItem( '<button id="settle-translate-translate-btn" class="btn btn-primary">' . wfMessage( 'settle-translate-btn-translate' ) . '</button>' );
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<form enctype="application/x-www-form-urlencoded" target="_blank" method="post">' . self::generateFormFields( $parser, 'Card', $lang ) . '</form>';
		$html .= '</div>';

		return array(
			$html,
			'isHTML'  => true,
			'noparse' => true
		);
	}

	/**
	 * @param Parser $parser
	 * @param string $templateName
	 * @param string $lang
	 *
	 * @return string
	 */
	public static function generateFormFields( $parser, $templateName, $lang )
	{
		global $wgLanguageCode;

		$html = '';
		$html .= '<input type="hidden" name="'.$templateName.'[Foreign source]" value="'.$wgLanguageCode.':'.$parser->getTitle()->getBaseText().'" />';

		$page = WikiPage::factory( $parser->getTitle() );
		$pageText = $page->getContent()->getWikitextForTransclusion();

		$re = "/\\|([^=]+)=([^\\|^\\}]+)/m";
		$matches = array();
		preg_match_all($re, $pageText, $matches);

		if( count($matches) > 2 ) {
			foreach ($matches[0] as $i => $matchGroup) {
				$paramName = $matches[1][$i];
				$paramValue = $matches[2][$i];
				//$html .= '<input type="hidden" name="'.$templateName.'['.$paramName.']" value="'.str_replace("\n", " ", $paramValue).'" />';
				$html .= '<input type="hidden" name="'.$templateName.'['.$paramName.']" value="'.str_replace('"', "'", $paramValue).'" />';
			}
		}

		return $html;

	}

	/**
	 * @param Parser $parser
	 * @param string $templateName
	 * @param string $lang
	 * @param string $targetPageName
	 *
	 * @return string
	 */
	public static function generateDomainLink( $parser, $templateName, $lang, $targetPageName = '' )
	{
		global $wgSettleTranslateDomains, $wgLanguageCode;
		if( !array_key_exists($lang, $wgSettleTranslateDomains) ) {
			return '';
		}

		$domain = '//' . $wgSettleTranslateDomains[ $lang ];

		// Build up form array, a bit dirty - may be refined in future
		$link = $domain . '/index.php?title=Special:FormEdit/'.$templateName.'/'.$targetPageName; // No page title here
		$link .= '&translateMode=true';
		$link .= '&translateFrom='.$wgLanguageCode;

		return urlencode( $link );

	}

}
