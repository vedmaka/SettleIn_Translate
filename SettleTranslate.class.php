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
	 * @return string
	 */
	public static function renderForeign( $parser ) {

		global $wgLanguageCode;

		$html = '';

		if( !$parser->getTitle() || !$parser->getTitle()->exists() ) {
			return '';
		}

		$currentTitle = $wgLanguageCode . ':' . $parser->getTitle()->getBaseText();
		$selfSource = self::extractSelfSource( $parser->getTitle() );

		$html .= '<div class="foreign-render-list" data-currenttitle="'.$currentTitle.'" data-selfsource="'.$selfSource.'">';
		$html .= wfMessage( 'settle-translate-foreign-title' )->plain();
		$html .= '<span class="foreign-list-result-text">' . wfMessage( 'settle-translate-foreign-title-not-found' )->plain() . '</span>';
		$html .= '</div>';

		$parser->getOutput()->addModules('ext.settletranslate.foreign');

		return array(
			$html,
			'isHTML'  => true,
			'noparse' => true,
			'markerType' => 'none'
		);

	}

	/**
	 * @param Parser $parser
	 * @return string
	 */
	public static function renderLink( $parser ) {

		global $wgSettleTranslateDomains;

		$html = '';

		$title = $parser->getTitle();

		if( !$title || !$title->exists() ) {
			return '';
		}

		$parser->getOutput()->addModules( 'ext.settletranslate.window' );

		$html .= $parser->insertStripItem(
			'<a id="translate-link" href="#" type="button" class="card-special-skin-link" data-toggle="modal" data-target=".translate-window"> '
			. wfMessage( 'settle-translate-link-text' )->plain() . '</a>' );

		$html .= '<div id="translate-window" class="modal fade bs-example-modal-sm translate-window" role="dialog">';
		$html .= '<div class="modal-dialog modal-sm">';
		$html .= '<div class="modal-content">';
		$html .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title" id="mySmallModalLabel">' . wfMessage( 'settle-translate-modal-title' ) . '</h4></div>';
		$html .= '<div class="modal-body">';
		$html .= '<p>' . wfMessage( 'settle-translate-intro-text' )->plain() . '</p>';
		$select = '<select class="form-control"><option></option>';
		foreach ( $wgSettleTranslateDomains as $lang => $domain ) {
			$langText = Language::fetchLanguageName( $lang );
			if ( ! $langText ) {
				continue;
			}
			$select .= '<option data-lang="'.$lang.'" value="' . self::generateDomainLink( $parser, 'Card', $lang ) . '">' . $langText . '</option>';
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
			'noparse' => true,
			'markerType' => 'none'
		);
	}

	public static function makeInput( $name, $value )
	{
		return '<input type="hidden" name="'.$name.'" value="'.str_replace('"', "", str_replace( "\n", "", $value ) ).'" />';
	}


	/**
	 * @param Title $title
	 *
	 * @return bool|mixed
	 * @throws MWException
	 */
	public static function extractSelfSource( $title ) {

		if( !$title->exists() ) {
			return false;
		}

		$page = WikiPage::factory( $title );
		$pageText = $page->getContent()->getWikitextForTransclusion();

		//Country
		$matches = array();
		$match = preg_match('#\|Foreign source=(.+)#', $pageText, $matches);
		if( $match ) {
			return $matches[1];
		}

		return false;

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

		//TODO: DO NOT PASS TITLE TOO!
		//Title
		/*$matches = array();
		$match = preg_match('#\|Title=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Title]', $matches[1] );
		}*/

		//Country
		$matches = array();
		$match = preg_match('#\|Country=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Country]', $matches[1] );
		}

		//Country
		$matches = array();
		$match = preg_match('#\|Tags=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Tags]', $matches[1] );
		}

		//Application name
		$matches = array();
		$match = preg_match('#\|Application name=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Application name]', $matches[1] );
		}

		//Link to application
		$matches = array();
		$match = preg_match('#\|Link to application=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Link to application]', $matches[1] );
		}

		//Total cost
		$matches = array();
		$match = preg_match('#\|Total cost=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Total cost]', $matches[1] );
		}

		//Total cost currency
		$matches = array();
		$match = preg_match('#\|Total cost currency=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Total cost currency]', $matches[1] );
		}

		//Processing time
		$matches = array();
		$match = preg_match('#\|Processing time=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Processing time]', $matches[1] );
		}

		//Difficulty
		$matches = array();
		$match = preg_match('#\|Difficulty=(.+)#', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Difficulty]', $matches[1] );
		}

		// ...

		// Tips - multiple
		$matches = array();
		preg_match_all('/\\{\\{Card tips([^\\}\\}]+)/m', $pageText, $matches);
		if( count($matches) > 1 ) {
			foreach ( $matches[1] as $templateNumber => $itemContent ) {
				$re = "/\\|([^=]+)=([^\\|^\\}]+)/m";
				$matches2 = array();
				preg_match_all($re, $itemContent, $matches2);
				if( count($matches2) > 2 ) {
					foreach ($matches2[0] as $i => $matchGroup) {
						$paramName = $matches2[1][$i];
						$paramValue = $matches2[2][$i];
						$html .= self::makeInput( 'Card tips['.($templateNumber + 1).']['.$paramName.']', $paramValue );
					}
				}
			}
		}

		// Step by step - multiple
		$matches = array();
		preg_match_all('/\\{\\{Card step by step([^\\}\\}]+)/m', $pageText, $matches);
		if( count($matches) > 1 ) {
			foreach ( $matches[1] as $templateNumber => $itemContent ) {
				$re = "/\\|([^=]+)=([^\\|^\\}]+)/m";
				$matches2 = array();
				preg_match_all($re, $itemContent, $matches2);
				if( count($matches2) > 2 ) {
					foreach ($matches2[0] as $i => $matchGroup) {
						$paramName = $matches2[1][$i];
						$paramValue = $matches2[2][$i];
						$html .= self::makeInput( 'Card step by step['.($templateNumber + 1).']['.$paramName.']', $paramValue );
					}
				}
			}
		}

		// Documents - multiple
		$matches = array();
		preg_match_all('/\\{\\{Card documents([^\\}\\}]+)/m', $pageText, $matches);
		if( count($matches) > 1 ) {
			foreach ( $matches[1] as $templateNumber => $itemContent ) {
				$re = "/\\|([^=]+)=([^\\|^\\}]+)/m";
				$matches2 = array();
				preg_match_all($re, $itemContent, $matches2);
				if( count($matches2) > 2 ) {
					foreach ($matches2[0] as $i => $matchGroup) {
						$paramName = $matches2[1][$i];
						$paramValue = $matches2[2][$i];
						$html .= self::makeInput( 'Card documents['.($templateNumber + 1).']['.$paramName.']', $paramValue );
					}
				}
			}
		}

		// Costs - multiple
		$matches = array();
		preg_match_all('/\\{\\{Card costs([^\\}\\}]+)/m', $pageText, $matches);
		if( count($matches) > 1 ) {
			foreach ( $matches[1] as $templateNumber => $itemContent ) {
				$re = "/\\|([^=]+)=([^\\|^\\}]+)/m";
				$matches2 = array();
				preg_match_all($re, $itemContent, $matches2);
				if( count($matches2) > 2 ) {
					foreach ($matches2[0] as $i => $matchGroup) {
						$paramName = $matches2[1][$i];
						$paramValue = $matches2[2][$i];
						$html .= self::makeInput( 'Card costs['.($templateNumber + 1).']['.$paramName.']', $paramValue );
					}
				}
			}
		}

		// Card links - multiple
		$matches = array();
		preg_match_all('/\\{\\{Card links([^\\}\\}]+)/m', $pageText, $matches);
		if( count($matches) > 1 ) {
			foreach ( $matches[1] as $templateNumber => $itemContent ) {
				$re = "/\\|([^=]+)=([^\\|^\\}]+)/m";
				$matches2 = array();
				preg_match_all($re, $itemContent, $matches2);
				if( count($matches2) > 2 ) {
					foreach ($matches2[0] as $i => $matchGroup) {
						$paramName = $matches2[1][$i];
						$paramValue = $matches2[2][$i];
						$html .= self::makeInput( 'Card links['.($templateNumber + 1).']['.$paramName.']', $paramValue );
					}
				}
			}
		}

		// Card forms - multiple
		$matches = array();
		preg_match_all('/\\{\\{Card forms([^\\}\\}]+)/m', $pageText, $matches);
		if( count($matches) > 1 ) {
			foreach ( $matches[1] as $templateNumber => $itemContent ) {
				$re = "/\\|([^=]+)=([^\\|^\\}]+)/m";
				$matches2 = array();
				preg_match_all($re, $itemContent, $matches2);
				if( count($matches2) > 2 ) {
					foreach ($matches2[0] as $i => $matchGroup) {
						$paramName = $matches2[1][$i];
						$paramValue = $matches2[2][$i];
						$html .= self::makeInput( 'Card forms['.($templateNumber + 1).']['.$paramName.']', $paramValue );
					}
				}
			}
		}

		//Content
		/*$matches = array();
		$match = preg_match('/\\|Content=([^\\|]+)/m', $pageText, $matches);
		if( $match ) {
			$html .= self::makeInput( $templateName.'[Content]', $matches[1] );
		}*/

		return $html;

		//TODO: for the future versions

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
