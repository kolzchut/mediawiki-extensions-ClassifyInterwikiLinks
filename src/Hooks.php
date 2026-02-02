<?php

namespace MediaWiki\Extension\ClassifyInterwikiLinks;

use Config;
use MediaWiki\Config\ConfigFactory;
use MediaWiki\Linker\Hook\HtmlPageLinkRendererEndHook;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Page\WikiPageFactory;
use MediaWiki\Title\Title;

/**
 * Hooks for ClassifyInterwikiLinks extension
 *
 * @file
 * @ingroup Extensions
 */
class Hooks implements HtmlPageLinkRendererEndHook {

	private Config $config;
	private WikiPageFactory $wikiPageFactory;

	/**
	 * @param ConfigFactory $configFactory
	 * @param WikiPageFactory $wikiPageFactory
	 */
	public function __construct(
		ConfigFactory $configFactory,
		WikiPageFactory $wikiPageFactory
	) {
		$this->config = $configFactory->makeConfig( 'ClassifyInterwikiLinks' );
		$this->wikiPageFactory = $wikiPageFactory;
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
	 * @param bool|null $isKnown
	 * @param string|null &$text
	 * @param array &$attribs
	 * @param mixed &$ret
	 * @return void
	 */
	public function onHtmlPageLinkRendererEnd(
		$linkRenderer,
		$target,
		$isKnown,
		&$text,
		&$attribs,
		&$ret
	): void {
		$originalTarget = Title::newFromLinkTarget( $target );
		$finalTarget = $originalTarget;

		if ( $this->config->get( 'ClassifyInterwikiLinksFollowRedirects' )
			&& $originalTarget->isRedirect()
		) {
			$page = $this->wikiPageFactory->newFromID( $originalTarget->getArticleID() );
			if ( $page ) {
				$finalTarget = $page->getRedirectTarget();
			}
		}

		if ( !isset( $attribs['class'] ) ) {
			$attribs['class'] = '';
		}

		if ( $finalTarget && $finalTarget->isExternal() ) {
			$interwiki = $finalTarget->getInterwiki();
			// Did we get here through a redirect? Mark it as so:
			if ( $originalTarget->isRedirect() ) {
				$attribs['class'] .= ' mw-redirect-extiw';
			}
			$attribs['class'] .= ' extiw-' . $interwiki;
		}
	}
}

