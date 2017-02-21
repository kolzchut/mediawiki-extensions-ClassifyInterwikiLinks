<?php
/**
 * Hooks for ClassifyInterwikiLinks extension
 *
 * @file
 * @ingroup Extensions
 */


class ClassifyInterwikiLinksHooks {
	private static $config;

	/**
	 * Get our config object.
	 * @return Config
	 */
	private static function getConfig() {
		if ( !self::$config instanceof Config ) {
			self::$config = \MediaWiki\MediaWikiServices::getInstance()
				->getConfigFactory()
				->makeConfig( 'ClassifyInterwikiLinks' );
		}
		return self::$config;
	}



	/**
	 * If the link is to an external interwiki, add a class with that interwiki prefix.
	 * If the link is to a redirect to an external interwiki, add both the above class *and*
	 * .mw-redirect-extiw;
	 *
	 * @param $dummy
	 * @param Title $originalTarget
	 * @param array $options
	 * @param $text
	 * @param array $attribs
	 * @param $ret
	 *
	 * @return bool
	 */
	public static function onLinkEnd(
		$dummy, Title $originalTarget, array $options, &$text, array &$attribs, &$ret
	) {
		$finalTarget = $originalTarget;
		if ( self::getConfig()->get( 'ClassifyInterwikiLinksFollowRedirects' )
		     && $originalTarget->isRedirect()
		) {
			$page = WikiPage::newFromID( $originalTarget->getArticleID() );
			$finalTarget = $page->getRedirectTarget();
		}

		if ( $finalTarget->isExternal() ) {
			$interwiki = $finalTarget->getInterwiki();
			// Did we get here through a redirect? Mark it as so:
			$attribs['class'] .= $originalTarget->isRedirect() ? ' mw-redirect-extiw' : '';
			$attribs['class'] .= ' extiw-'.$interwiki;
		}

		return true;
	}
}

