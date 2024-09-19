<?php
class Bbc_Guid_Fixer extends Plugin {
	public function about() {
		return [
			null, // version
			'Strips fragments from BBC RSS feed article GUIDs!', // description
			'wn', // author
			false, // is system
			'https://github.com/supahgreg/ttrss-bbc-guid-fixer', // more info URL
		];
	}

	public function api_version() {
		return 2;
	}

	/**
	 * @param PluginHost $host
	 **/
	public function init($host): void {
		$host->add_hook($host::HOOK_FEED_PARSED, $this);
	}

	/**
	 * @param FeedParser $parser
	 * @param int $feed_id
	 */
	function hook_feed_parsed($parser, $feed_id): void {
		if (!str_contains($parser->get_link(), 'bbc.co.uk'))
			return;

		/** @var FeedItem_Common $item */
		foreach ($parser->get_items() as $item) {
			/** @var DOMElement|null */
			$guid_element = $item->get_element()->getElementsByTagName('guid')->item(0);

			if ($guid_element
				&& str_contains($guid_element->nodeValue ?? '', '#')
				&& $guid_element->getAttribute('isPermaLink') === 'false') {
				$guid_element->nodeValue = strstr($guid_element->nodeValue, needle: '#', before_needle: true);
			}
		}
	}
}
