<?php

class SettleTranslateApi extends ApiBase {

	public function execute()
	{

		$formattedData = array();

		$params = $this->extractRequestParams();
		$page = $params['page'];
		$sqi = new \SQI\SemanticQueryInterface();

		$result = $sqi->condition('Foreign source', $page)->toArray();
		if( count($result) ) {
			foreach( $result as $subject ) {
				$title = $subject['title'];
				$formattedData[] = array(
					'id' => $title->getArticleID(),
					'title' => $title->getBaseText(),
					'link' => $title->getFullURL()
				);
			}
		}

		$this->getResult()->addValue( null, $this->getModuleName(), $formattedData );

	}

	public function getAllowedParams()
	{
		return array(
			'page' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => 'string'
			)
		);
	}

}