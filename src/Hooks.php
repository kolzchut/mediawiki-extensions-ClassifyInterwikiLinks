<?php

namespace MediaWiki\Extension\ClassifyInterwikiLinks;

use Config;
use MediaWiki\Config\ConfigFactory;
use MediaWiki\Linker\Hook\HtmlPageLinkRendererEndHook;
use MediaWiki\Page\RedirectLookup;
use MediaWiki\Title\Title;

/**
 * Hooks for ClassifyInterwikiLinks extension
 */
class Hooks implements HtmlPageLinkRendererEndHook {

	private Config $config;
	private RedirectLookup $redirectLookup;

	/**
	 * @param ConfigFactory $configFactory
	 * @param RedirectLookup $redirectLookup
	 */
	public function __construct(
		ConfigFactory $configFactory,
		RedirectLookup $redirectLookup
	) {
		$this->config = $configFactory->makeConfig( 'ClassifyInterwikiLinks' );
		$this->redirectLookup = $redirectLookup;
	}

	/**
	 * If the link is to an external interwiki, add a class with that interwiki prefix.
	 * If the link is to a redirect to an external interwiki, add both the above class *and*
	 * .mw-redirect-extiw
	 *
	 * @inheritDoc
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
			$redirectTarget = $this->redirectLookup->getRedirectTarget( $originalTarget );
			if ( $redirectTarget ) {
				$finalTarget = $redirectTarget;
			}
		}

		if ( !isset( $attribs['class'] ) ) {
			$attribs['class'] = '';
		}

		if ( $finalTarget && $finalTarget->isExternal() ) {
			$interwiki = $finalTarget->getInterwiki();
			// Did we get here through a redirect? Mark it as so:
			if ( isset( $redirectTarget ) && $redirectTarget ) {
				$attribs['class'] .= ' mw-redirect-extiw';
			}
			$attribs['class'] .= ' extiw-' . $interwiki;
		}
	}
}
