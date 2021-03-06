<?php

/**
 * Simple editor for lists.
 */
class ListEditor {
	/**
	 * This function hooks into InlineEditorMark and marks the lists.
	 * @param $inlineEditorText InlineEditorText
	 */
	public static function mark( &$inlineEditorText ) {
		// get the original wikitext
		$text = $inlineEditorText->getWikiOriginal();

		$matches = array();
		preg_match_all( '/(\n|^)(([*#;:].*(\n|$))+)/', $text, $matches, PREG_OFFSET_CAPTURE );

		foreach ( $matches[2] as $match ) {
			$start = $match[1];
			$end   = $start + strlen( $match[0] );

			// do not include the trailing newline
			if ( substr( $match[0], -1 ) == "\n" ) $end--;

			$inlineEditorText->addMarking( new InlineEditorMarking( $start, $end, 'listEditorElement inlineEditorBasic', true, false ) );
		}

		return true;
	}
}
