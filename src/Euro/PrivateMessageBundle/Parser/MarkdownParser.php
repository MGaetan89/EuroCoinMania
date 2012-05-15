<?php

namespace Euro\PrivateMessageBundle\Parser;

use Knp\Bundle\MarkdownBundle\Parser\MarkdownParser as BaseParser;

class MarkdownParser extends BaseParser {

	public function transform($text) {
		$text = parent::transform($text);
		$text = preg_replace('`<img src="([^"]+)" alt="([^"]+)" />`', '<a class="span6 thumbnail" href="\\1" target="_blank" title="\\2"><img alt="\\2" src="\\1" /></a>', $text);

		return $text;
	}

}
