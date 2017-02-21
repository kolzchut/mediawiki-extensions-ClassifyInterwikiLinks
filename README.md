# MediaWiki extension ClassifyInterwikiLinks

This extension does a very simple thing - it adds a css class to every interwiki link.
Currently MediaWiki only adds the "extiw" css class to such links, so we add classes
of 'extiw-theinterwikiprefix'; e.g., for interwiki links to Wikipedia,
the class added will be 'extiw-wikipedia'.

This allows very easy styling using CSS later on.

This extension also follows redirects by default; that is, if your link
is to a redirect page, and that redirect is to an interwiki target,
it will also get the same class, as well as an additional general class,
'mw-redirect-extiw'.
As this option is somewhat more expensive, you can turn it off by setting
`$wgClassifyInterwikiLinksFollowRedirects = false;` in your `LocalSettings.php`.
