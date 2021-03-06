<?php
use Wikia\Search\Config, Wikia\Search\QueryService\Factory, Wikia\Search\QueryService\DependencyContainer;
class TvApiController extends WikiaApiController {

	//const PARAMETER_NAMESPACES = 'namespaces';
	const LIMIT_SETTING = 1;
	const WIKI_LIMIT = 5;
	const HUB_SETTING = 'Entertainment';
	const RANK_SETTING = 'default';
	const LANG_SETTING = 'en';
	const NAMESPACE_SETTING = 0;
	const API_URL = 'api/v1/Articles/AsSimpleJson?id=';
	const MINIMAL_WIKIA_SCORE = 0.5;
	const MINIMAL_ARTICLE_SCORE = 1.7;
	const WIKIA_URL_REGEXP = '~^(http(s?)://)(([^\.]+)\.wikia\.com)~';
	const RESPONSE_CACHE_VALIDITY = 86400; /* 24h */
	const PARAM_ARTICLE_QUALITY = 'minArticleQuality';
	/**
	 * @var Array wikis
	 */
	protected $wikis = [];


	public function getEpisode() {
		global $wgStagingEnvironment, $wgDevelEnvironment;
		if ( !$this->setWikiVariables() ) {
			throw new NotFoundApiException();
		}

		$minQuality = $this->request->getVal( self::PARAM_ARTICLE_QUALITY );
		if ( $minQuality !== null ) {
			$minQuality = (int)$minQuality;
		}


		foreach( $this->wikis as $wiki ) {
			$responseValues = null;
			$wikiId = $wiki['id'];
			$url = $wiki['url'];
			$responseValues = $this->getExactMatch( $wiki['id'] );
			if ( $responseValues === null ) {
				$config = $this->getConfigFromRequest( $wiki['id'] );
				$responseValues = $this->getResponseFromConfig( $config, $wiki['id'] );
			}

			if ( $responseValues !== null ) {
				if ( ( $minQuality == null ) ||
					( $responseValues[ 'quality' ] !== null && $responseValues[ 'quality' ] >= $minQuality )
				) {
					break;
				}

				$responseValues = null;
			}
		}

		if ( empty($responseValues) ) {
			throw new NotFoundApiException();
		}

		if ( empty( $responseValues[ 'contentUrl' ] ) ) { //only for unit test
			$responseValues[ 'contentUrl' ] = $url . self::API_URL . $responseValues[ 'articleId' ];
		}
		if ( $wgStagingEnvironment || $wgDevelEnvironment ) {
			$responseValues[ 'contentUrl' ] = preg_replace_callback( self::WIKIA_URL_REGEXP, array( $this, 'replaceHost' ), $responseValues[ "contentUrl" ] );
			$responseValues[ 'url' ] = preg_replace_callback( self::WIKIA_URL_REGEXP, array( $this, 'replaceHost' ), $responseValues[ "url" ] );
		}

		$responseValues = array_merge( [ 'wikiId' => (int) $wikiId ], $responseValues );

		$response = $this->getResponse();
		$response->setValues( $responseValues );

		$response->setCacheValidity(self::RESPONSE_CACHE_VALIDITY);
	}

	/**
	 * Callback function for preg_replace
	 * @param $details
	 * @return string
	 */
	protected function replaceHost( $details ) {
		return $details[ 1 ] . WikiFactory::getCurrentStagingHost( $details[ 4 ], $details[ 3 ] );
	}

	/**
	 * Query Solr for articleQuality
	 * @param $wikiId int
	 * @param $articleId int
	 * @return array
	 */
	protected function getQualityFromSolr( $wikiId, $articleId ) {
		$config = $this->getConfigById( $wikiId, $articleId );
		return ( new Factory )->getFromConfig( $config )->searchAsApi( ['article_quality_i' => 'quality'  ], false );
	}

	/**
	 * Get article quality from solr
	 * @param $wikiId int
	 * @param $articleId int
	 * @return null|int
	 */
	protected function getArticleQuality( $wikiId, $articleId ) {
		$responseValues = $this->getQualityFromSolr( $wikiId, $articleId );
		if ( !empty( $responseValues ) && isset( $responseValues[ 0 ][ 'quality' ] ) ) {
			return $responseValues[ 0 ][ 'quality' ];
		}

		return null;
	}

	protected function getExactMatch( $wikiId ) {
		$query = $this->request->getVal( 'episodeName', null );
		if ( $query !== null ) {
			return $this->getTitle( $query, $wikiId );
		}
		return null;
	}

	protected function createTitle($text, $wikiId)
	{
		return GlobalTitle::newFromText( $text, NS_MAIN, $wikiId );
	}

	protected function getTitle( $text, $wikiId ) {
		//try exact phrase
		$underscoredText = str_replace( ' ', '_', $text );
		$title = $this->createTitle( $underscoredText, $wikiId );
		if ( !$title->exists() ) {
			$serializedText = str_replace( ' ', '_', ucwords( strtolower( $text ) ) );
			$title = $this->createTitle( $serializedText, $wikiId );
		}
		if ( $title->isRedirect() ) {
			$title = $title->getRedirectTarget();
		}
		if ( $title->exists() ) {
			$articleId = (int)$title->getArticleID();
			return [
				'articleId' => $articleId,
				'title' => $title->getText(),
				'url' => $title->getFullURL(),
				'quality' => $this->getArticleQuality( $wikiId, $articleId )
			];
		}
		return null;
	}

	protected function setWikiVariables(){
		$config = $this->getConfigCrossWiki();
		$resultSet = (new Factory)->getFromConfig( $config )->search();
		$found = false;

		foreach( $resultSet->getResults() as $result ) {
			if ( $result['id'] && $result['url'] && $result['score'] > static::MINIMAL_WIKIA_SCORE ) {
				$this->wikis[] = [ 'id' => $result['id'], 'url' => $result['url'] ];
				$found = true;
			}
		}

		return $found;
	}

	protected function getConfigCrossWiki() {
		$request = $this->getRequest();
		$searchConfig = new Wikia\Search\Config;
		$query = $request->getVal( 'seriesName', null );
		if ( empty( $query ) || $query === null ) {
			throw new InvalidParameterApiException( 'seriesName' );
		}
		$searchConfig->setQuery( $query )
			->setLimit( static::WIKI_LIMIT )
			->setRank( static::RANK_SETTING )
			->setInterWiki( true )
			->setCommercialUse( $this->hideNonCommercialContent() )
			->setLanguageCode( $request->getVal( 'lang', static::LANG_SETTING ) )
			->setHub( static::HUB_SETTING )
		;
		return $searchConfig;
	}

	/**
	 * Inspects request and sets config accordingly.
	 * @return Wikia\Search\Config
	 */
	protected function getConfigFromRequest( $wikiId ) {
		$request = $this->getRequest();
		$searchConfig = new Wikia\Search\Config;

		$searchConfig->setQuery( $request->getVal( 'episodeName', null ) )
			->setLimit( static::LIMIT_SETTING )
			->setPage( static::LIMIT_SETTING )
			->setLanguageCode( $request->getVal( 'lang', static::LANG_SETTING ) )
			->setRank( static::RANK_SETTING )
			->setWikiId( $wikiId )
			->setMinArticleQuality( $request->getInt(static::PARAM_ARTICLE_QUALITY) )
			->setVideoSearch( false )
			->setOnWiki(true)
			->setNamespaces( [static::NAMESPACE_SETTING] );
		;

		return $searchConfig;
	}

	/**
	 * Prepare config with wikiId and articleId
	 * @param $wikiId int
	 * @param $articleId int
	 * @return Wikia\Search\Config
	 */
	protected function getConfigById( $wikiId, $articleId ) {
		$searchConfig = new Wikia\Search\Config;
		$searchConfig->setQuery( '*' )
			->setLimit( 1 )
			->setPage( 1 )
			->setWikiId( (int)$wikiId )
			->setVideoSearch( false )
			->setOnWiki( true )
			->setPageId( (int)$articleId )
			->setNamespaces( [ static::NAMESPACE_SETTING ] );
		return $searchConfig;
	}

	protected function getResponseFromConfig( Wikia\Search\Config $searchConfig, $wikiId ) {
		if (! $searchConfig->getQuery()->hasTerms() ) {
			throw new InvalidParameterApiException( 'episodeName' );
		}

		$responseValues = (new Factory)->getFromConfig( $searchConfig )->searchAsApi(
			[ 'pageid' => 'articleId', 'title', 'url', 'score', 'article_quality_i' => 'quality' ],
			true
		);

		//post processing
		if ( !empty( $responseValues[ 'items' ] ) ) {
			$responseValues = $responseValues[ 'items' ][ 0 ];
			if ( $responseValues['score'] < static::MINIMAL_ARTICLE_SCORE ) {
				return null;
			}
			//remove score value from results
			unset( $responseValues['score'] );

			$subpage = strpos( $responseValues['title'], '/' );
			if ( $subpage !== false ) {
				//we found subpage, return only the main page then
				$main = substr( $responseValues['title'], 0, $subpage );
				$result = $this->getTitle( $main, $wikiId );
				if ( $result !== null ) {
					return $result;
				}
			}
			return $responseValues;
		}
		return null;
	}
}
