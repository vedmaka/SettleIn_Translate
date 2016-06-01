<?php

/**
 * SettleTranslate SpecialPage for SettleTranslate extension
 *
 * @file
 * @ingroup Extensions
 */
class SpecialSettleTranslate extends SpecialPage
{
    public function __construct()
    {
        parent::__construct( 'SettleTranslate' );
    }

    /**
     * Show the page to the user
     *
     * @param string $sub The subpage string argument (if any).
     *  [[Special:SettleTranslate/subpage]].
     */
    public function execute( $sub )
    {
        $out = $this->getOutput();

        $out->setPageTitle( $this->msg( 'settletranslate-helloworld' ) );

        $out->addHelpLink( 'How to become a MediaWiki hacker' );

        $out->addWikiMsg( 'settletranslate-helloworld-intro' );
    }

    protected function getGroupName()
    {
        return 'other';
    }
}
