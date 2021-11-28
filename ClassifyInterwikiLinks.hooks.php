<?php

use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;

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
	 * HtmlPageLinkRendererEnd hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/HtmlPageLinkRendererEnd
	 *
	 * Used to change interwiki links
	 *
	 * @param LinkRenderer $linkRenderer
	 * @param LinkTarget $target
	 * @param $isKnown
	 * @param &$text
	 * @param &$attribs []
	 * @param &$ret
	 *
	 * @return bool
	 */
	public static function onHtmlPageLinkRendererEnd(
		LinkRenderer $linkRenderer, LinkTarget $target, $isKnown, &$text, &$attribs, &$ret
	) {
		$originalTarget = Title::newFromLinkTarget( $target );
		$finalTarget = $originalTarget;
		if ( self::getConfig()->get( 'ClassifyInterwikiLinksFollowRedirects' )
		     && $originalTarget->isRedirect()
		) {
			$page = WikiPage::newFromID( $originalTarget->getArticleID() );
			$finalTarget = $page->getRedirectTarget();
		}

		isset( $attribs['class'] ) || $attribs['class'] = "";
		if ( $finalTarget && $finalTarget->isExternal() ) {
			$interwiki = $finalTarget->getInterwiki();
			// Did we get here through a redirect? Mark it as so:
			$attribs['class'] .= $originalTarget->isRedirect() ? ' mw-redirect-extiw' : '';
			$attribs['class'] .= ' extiw-'.$interwiki;
		}

		return true;
	}
}

