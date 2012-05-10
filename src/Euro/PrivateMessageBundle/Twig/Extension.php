<?php

namespace Euro\PrivateMessageBundle\Twig;

class Extension extends \Twig_Extension {

	public function getFilters() {
		return array(
			'markdown' => new \Twig_Filter_Method($this, 'markdownFilter'),
		);
	}

	public function getName() {
		return 'euro_privatemessagebundle_extension';
	}

	public function markdownFilter($text) {
		// Bold
		$text = preg_replace('#\*\*(.+)\*\*#U', '<b>\\1</b>', $text);

		// Italic
		$text = preg_replace('#//(.+)//#U', '<i>\\1</i>', $text);

		// Strike
		$text = preg_replace('#--(.+)--#U', '<s>\\1</s>', $text);

		// Underline
		$text = preg_replace('#__(.+)__#U', '<u>\\1</u>', $text);

		// Image
		$text = preg_replace('#!\[([^]]+)\]\(([^)]+)\)#U', '<img alt="\\1" src="\\2" title="\\1" />', $text);

		// Link
		$text = preg_replace('#\[([^]]+)\]\(([^)]+)\)#U', '<a href="\\2">\\1</a>', $text);

		// Quote
		$text = preg_replace('# > (.+)(?:\n|$)#U', '<blockquote><p>\\1</p></blockquote>', $text);

		// Line break
		$text = nl2br($text);

		return $text;
	}

}
