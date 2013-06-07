<?php

class GWTWikiRepository {
	/**
	 * @var DatabaseMysql|null
	 */
	private $databaseConnection;

	function __construct( $databaseConnection = null ) {
		if ( $databaseConnection == null ) {
			global $wgExternalSharedDB;
			$app = F::app();
			$databaseConnection = $app->wf->getDB( DB_MASTER, array(), $wgExternalSharedDB);
		}
		$this->databaseConnection = $databaseConnection;
	}

	/**
	 * @param $resultObject
	 * @return GWTWiki
	 */
	private function materialize( $resultObject ) {
		return new GWTWiki( $resultObject->wiki_id, $resultObject->user_id, $resultObject->upload_date, $resultObject->page_count );
	}

	/**
	 * @param $queryResult
	 * @return array
	 */
	private function materializeList( $queryResult ) {
		$list = array();
		while ( $obj = $queryResult->fetchObject() ) {
			$list[] = $this->materialize($obj);
		}
		return $list;
	}
	/*
	public function all() {
		$result = $this->databaseConnection->select("webmaster_sitemaps", array("user_id", "wiki_id", "upload_date"));
		return $this->materializeList($result);
	}
	*/
	/**
	 * @return array
	 */
	public function allUnassigned() {
		$result = $this->databaseConnection->select("webmaster_sitemaps"
			, array("user_id", "wiki_id", "upload_date", "page_count")
			, array("upload_date" => null ) );
		return $this->materializeList($result);
	}

	/**
	 * @param integer $wikiId
	 * @return array
	 */
	public function allByWikiId( $wikiId ) {
		$result = $this->databaseConnection->select("webmaster_sitemaps"
			, array("user_id", "wiki_id", "upload_date", "page_count")
			, array("wiki_id" => $wikiId));
		return $this->materializeList($result);
	}

	/**
	 * @param integer $wikiId
	 * @throws GWTException
	 * @return GWTWiki
	 */
	public function oneByWikiId( $wikiId ) {
		$wikis = $this->allByWikiId( $wikiId );
		if( count( $wikis ) == 1 ) return $wikis[0];
		throw new GWTException("Fetched wrong number of wikis (id=".$wikiId."). Expected 1, was " . count($wikis));
	}

	/**
	 * @param $wikiId
	 * @throws GWTException
	 */
	public function deleteAllByWikiId( $wikiId ) {
		$result = $this->databaseConnection->delete("webmaster_sitemaps"
			, array("wiki_id" => $wikiId));
		if( !$result ) throw new GWTException("Cannot delete sitemap by id ( id = " . $wikiId . " ) ");
	}

	/**
	 * @param $wikiId
	 * @return bool
	 */
	public function exists( $wikiId ) {
		$wikiId = $this->wikiToId( $wikiId );
		$res = $this->databaseConnection->select("webmaster_sitemaps",
			array('wiki_id'),
			array(
				"wiki_id" => $wikiId,
			),
			__METHOD__
		);
		if ( $res->fetchRow() ) return false;
		return true;
	}

	/**
	 * @param $wikiId
	 * @param int $userId
	 * @param null $uploadDate
	 * @param null $pageCount
	 * @return GWTWiki
	 */
	public function create( $wikiId, $userId = 0, $uploadDate = null, $pageCount = null  ) {
		return $this->insert( $wikiId, $userId, $uploadDate, $pageCount );
	}

	/**
	 * @param $wikiId
	 * @param int $userId
	 * @param null $uploadDate
	 * @param null $pageCount
	 * @return GWTWiki
	 * @throws Exception
	 */
	public function insert( $wikiId, $userId = 0, $uploadDate = null, $pageCount = null ) {
		if ( ! $this->databaseConnection->insert("webmaster_sitemaps", array(
			"wiki_id" => $wikiId,
			"user_id" => $userId,
			"upload_date" => $uploadDate,
			"page_count"  => $pageCount,
		))) {
			throw new Exception("can't insert wiki id = " . $wikiId);
		}
		return new GWTWiki( $wikiId, $userId, $uploadDate);
	}

	/**
	 * @param GWTWiki $gwtWikiObject
	 * @throws GWTException
	 */
	public function updateWiki( GWTWiki $gwtWikiObject ) {
		$userId = $gwtWikiObject->getUserId();
		$res = $this->databaseConnection->update("webmaster_sitemaps",
			array(
				"user_id" => $userId,
				"upload_date" => $gwtWikiObject->getUploadDate(),
				"page_count" => $gwtWikiObject->getPageCount(),
			),
			array(
				"wiki_id" => $gwtWikiObject->getWikiId(),
			));
		if( !$res ) throw new GWTException("Failed to update " . $gwtWikiObject->getUserId() . " " . $gwtWikiObject->getWikiId());
	}

	/**
	 * @param $wikiId
	 * @return int
	 * @throws Exception
	 */
	private function wikiToId( $wikiId ) {
		if( is_string( $wikiId ) ) {
			$wikiId = WikiFactory::UrlToID( $wikiId );
			if( $wikiId == null ) throw new Exception("Can't resolve " . $wikiId);
		}
		return $wikiId;
	}
}
