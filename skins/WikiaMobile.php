<?php
/**
 * WikiaMobile is a real skin, not an experimental playground for new features
 * 
 * @author Jakub Olek <bukaj.kelo(at)gmail.com>
 * @authore Federico "Lox" Lucignano <federico(at)wikia-inc.com>
 */
if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

//force HMTL5 compliance
global $wgHtml5;
$wgHtml5 = true;

class SkinWikiaMobile extends WikiaSkin {
	function __construct() {
		parent::__construct( 'WikiaMobileTemplate', 'wikiamobile' );
	}

	function getTopScripts(){
		$vars = [];
		$scripts = '';

		//this is run to grab all global variables
		$this->wf->runHooks( 'WikiaSkinTopScripts', array( &$vars, &$scripts, $this ) );

		//global variables
		//from Output class
		//and from ResourceLoaderStartUpModule
		$res = new ResourceVariablesGetter();
		$vars = array_intersect_key(
			$this->wg->Out->getJSVars() + $res->get() + $vars,
			array_flip( $this->wg->WikiaMobileIncludeJSGlobals )
		);

		return WikiaSkin::makeInlineVariablesScript( $vars ) . $scripts;
	}
}

class WikiaMobileTemplate extends WikiaBaseTemplate {
	function execute() {
		$this->app->setSkinTemplateObj( $this );
		$response = $this->app->sendRequest( 'WikiaMobileService', 'index' );
		$response->sendHeaders();
		$response->render();
	}
}