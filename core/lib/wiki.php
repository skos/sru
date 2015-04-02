<?php

class UFlib_Wiki {

	const TOKENS_ALL               = 0;

	const TOKENS_BLOCK             = 1;
	const TOKENS_BLOCK_HEADING     = 2;
	const TOKENS_BLOCK_BLOCKTEXT   = 3;
	const TOKENS_BLOCK_BLOCK       = 4;
	const TOKENS_BLOCK_BLOCK_LISTS = 5;
	const TOKENS_BLOCK_BLOCK_OTHER = 6;
	const TOKENS_BLOCK_SPECIAL     = 7;

	const TOKENS_DELIMITER         = 8;

	const TOKENS_INLINE            = 9;
	const TOKENS_INLINE_SPECIAL    = 10;
	const TOKENS_INLINE_PHRASE     = 11;
	const TOKENS_INLINE_INLINE     = 12;

	const TOKENS_INLINE_IN_HREF    = 13;

	const TOKENS_INLINE_ENTITIES   = 14;

	const TOKENS_SPECIAL           = 15;
	const TOKENS_INLINE_TEMP       = 16;

	const PARAGRAPHABLE            = 100;

	protected $tokens = array(
		self::TOKENS_BLOCK_HEADING => array(
			'!!!!!!' => 'Header6',
			'!!!!!'  => 'Header5',
			'!!!!'   => 'Header4',
			'!!!'    => 'Header3',
			'!!'     => 'Header2',
			'!'      => 'Header1',
		),
		self::TOKENS_BLOCK_BLOCKTEXT => array(
			'='    => 'Pre',
			'>'    => 'Blockquote',
			'-'    => false,
			'--'   => false,
			'---'  => false,
			'----' => 'Hr',
		),
		self::TOKENS_BLOCK_BLOCK_LISTS => array(
			'#' => 'ListOrdered',
			'*' => 'ListUnordered',
		),
		self::TOKENS_BLOCK_BLOCK_OTHER => array(
			'|' => 'Table',
			'%' => 'Div',
			false => 'Paragraph',
			'@'  => false,
			'@x' => false,
			'@v' => false,
			'@y' => false,
			'@m' => false,
			'@f' => false,
		),
		self::TOKENS_BLOCK_SPECIAL => array(
			'.'  => 'Class',
			'..' => 'Id',
			'@x:'=> 'Html',
			'@v:'=> 'VideoGoogle',
			'@y:'=> 'VideoYoutube',
			'@m:'=> 'VideoMetacafe',
			'@f:'=> 'Flash',
		),

		self::TOKENS_INLINE_SPECIAL => array(
			'/'  => false,
			'//' => 'LineBreak',
			'['  => 'Image',
		),
		self::TOKENS_INLINE_PHRASE => array(
			'**' => 'Strong',
			'*'  => 'Emphasy',
			','  => false,
			',,' => 'Quote',
			'_'  => 'Subscript',
			'^'  => 'Supscript',
		),
		self::TOKENS_INLINE_INLINE => array(
			'[[' => 'Href',
		),
		self::TOKENS_INLINE_ENTITIES => array(
			'-'   => 'Dash',
			'--'  => 'Ndash',
			'---' => 'Mdash',
		),

		self::TOKENS_SPECIAL => array(
			'\\' => 'Escape',
			'"'  => 'Quotation',
			'~'  => 'NoBreakSpace',
			'`'  => 'WordWrap',
			'.'  => 'Class',
			'..' => 'Id',
			'...'=> 'Hellip',
		),

		self::TOKENS_INLINE_TEMP => array(
		),
	);

	/**
	 * zdefiniowane zmienne
	 */
	protected $temps = array();
	
	/**
	 * lista parametrow dla nastepnego dodanego elementu
	 */
	protected $params = array();

	/**
	 * lista uzytych identyfikatorow
	 */
	protected $ids = array();

	public function __construct() {
		$this->generateScopes();
	}

	protected function reset() {
		$this->params = array();
		$this->tokens[self::TOKENS_INLINE_TEMP] = array();
		$this->level = 0;
	}

	/**
	 * laczy konteksty
	 */
	protected function generateScopes() {
		$tokens =& $this->tokens;

		$tokens[self::TOKENS_BLOCK_BLOCK] = $tokens[self::TOKENS_BLOCK_BLOCK_LISTS]
		                                  + $tokens[self::TOKENS_BLOCK_BLOCK_OTHER]
		                                  ;
		$tokens[self::TOKENS_BLOCK] = $tokens[self::TOKENS_BLOCK_HEADING]
		                            + $tokens[self::TOKENS_BLOCK_BLOCKTEXT]
		                            + $tokens[self::TOKENS_BLOCK_BLOCK]
		                            + $tokens[self::TOKENS_BLOCK_SPECIAL]
		                            ;

		$tokens[self::TOKENS_INLINE] = $tokens[self::TOKENS_INLINE_SPECIAL]
		                             + $tokens[self::TOKENS_INLINE_PHRASE]
		                             + $tokens[self::TOKENS_INLINE_INLINE]
		                             + $tokens[self::TOKENS_INLINE_ENTITIES]
		                             + $tokens[self::TOKENS_SPECIAL]
		                             + $tokens[self::TOKENS_INLINE_TEMP]
		                             ;
		$tokens[self::TOKENS_INLINE_IN_HREF] = $tokens[self::TOKENS_INLINE_SPECIAL]
		                                     + $tokens[self::TOKENS_INLINE_PHRASE]
		                                     + $tokens[self::TOKENS_INLINE_ENTITIES]
		                                     + $tokens[self::TOKENS_SPECIAL]
		                                     + $tokens[self::TOKENS_INLINE_TEMP]
		                                     ;
		$tokens[self::TOKENS_ALL] = $tokens[self::TOKENS_INLINE]
		                          + $tokens[self::TOKENS_BLOCK]
		                          ;
		$tokens[self::PARAGRAPHABLE] = array_flip($this->tokens[self::TOKENS_BLOCK_SPECIAL]);
	}

	public function render($txt, $scope=self::TOKENS_BLOCK, $level) {
		$this->reset();
		$this->level = $level;
		$txtOrig = $txt;
		$txt = str_replace("\r", '', $txt);

		$dom = new DOMDocument('1.0', 'UTF-8');
		$txt = explode("\n", $txt);
		if ($scope > self::TOKENS_DELIMITER) {
			$elements = $this->parseInline($dom, $txt, $scope);
		} else {
			$elements = $this->parseBlock($dom, $txt, $scope);
		}
		foreach ($elements as $element) {
			$dom->appendChild($element);
		}
		return $dom;
	}

	protected function blockAdd(DOMDocument &$dom, $type, array &$return, array &$allLines, &$lineNumber) {
		$funcName = 'addBlock'.$type;
		$elements = $this->{$funcName}($dom, $allLines, $lineNumber);
		foreach ($elements as $element) {
			$return[] = $element;
		}
	}

	protected function inlineAdd(DOMDocument &$dom, $type, &$return, &$txt, &$charNumber) {
		$tmp = explode('/', $type, 2);
		$type = $tmp[0];
		if (isset($tmp[1])) {
			$param = $tmp[1];
		} else {
			$param = null;
		}
		$funcName = 'addInline'.$type;
		$elements = $this->{$funcName}($dom, $txt, $charNumber, $param);
		foreach ($elements as $element) {
			$return[] = $element;
		}
	}

	/**
	 * glowny parser elementow blokowych
	 */
	protected function parseBlock(DOMDocument &$dom, array $lines, $scope, &$lineNumber=0) {
		$return = array();
		$paragraph = array();
		$linesCount = count($lines);
		$scopeArray =& $this->tokens[$scope];
		$paragraphable =& $this->tokens[self::PARAGRAPHABLE];	// ktore bloki nie przerywaja ciaglasci paragrafu
		if (isset($scopeArray[false])) {
			$findParagraph = true;
		} else {
			$findParagraph = false;
		}
		for ($i=&$lineNumber; $i<$linesCount; $i++) {
			$line = $lines[$i];
			$type = $this->findToken($line, $scopeArray);
			if ($findParagraph && '' !== $line && (false === $type || (isset($paragraphable[$type])) && count($paragraph))) {
				$paragraph[] = $line;
			} elseif (count($paragraph)) {
				$elements = $this->addBlockParagraph($dom, $paragraph);
				foreach ($elements as $element) {
					$return[] = $element;
				}
				$paragraph = array();
				if ($type && '' !== $line) {
					$this->blockAdd($dom, $type, $return, $lines, $i);
				}
			} else {
				if ($type && '' !== $line) {
					$this->blockAdd($dom, $type, $return, $lines, $i);
				}
			}
		}
		if (count($paragraph)) {	
			$elements = $this->addBlockParagraph($dom, $paragraph);
			foreach ($elements as $element) {
				$return[] = $element;
			}
		}
		return $return;
	}

	protected function parseInline(DOMDocument &$dom, $lines, $scope, &$charNumber=0) {
		$return = array();
		$txt = '';
		if (is_array($lines)) {
			$lines = implode("\n", $lines);
		}
		$charCount = strlen($lines);
		for ($i=&$charNumber; $i<$charCount; $i++) {
			$type = $this->findToken($lines, $this->tokens[$scope], $i);
			if (false === $type) {
				$txt .= $lines[$i];
			} elseif (count($txt)) {
				$elements = $this->addInlineText($dom, $txt);
				foreach ($elements as $element) {
					$return[] = $element;
				}
				$txt = '';
				$this->inlineAdd($dom, $type, $return, $lines, $i);
			} else {
				$this->inlineAdd($dom, $type, $return, $lines, $i);
			}
		}
		if (count($txt)) {
			$elements = $this->addInlineText($dom, $txt);
			foreach ($elements as $element) {
				$return[] = $element;
			}
		}
		return $return;
	}

	protected function addInlineId(DOMDocument &$dom, &$txt, &$charNumber) {
		if (!isset($txt[$charNumber-1]) || "\n" === $txt[$charNumber-1]) {
			$name = $this->findInlineContent($txt, "\n", $charNumber);
			$name = ltrim($name, '.');
			$this->addParam('id', $name);
			return array();
		} else {
			$charNumber++;
			return $this->addInlineText($dom, '..');
		}
	}

	protected function addInlineClass(DOMDocument &$dom, &$txt, &$charNumber) {
		if (!isset($txt[$charNumber-1]) || "\n" === $txt[$charNumber-1]) {
			$name = $this->findInlineContent($txt, "\n", $charNumber);
			$name = ltrim($name, '.');
			$this->addParam('class', $name);
			return array();
		} else {
			return $this->addInlineText($dom, '.');
		}
	}
	
	protected function addInlineDash(DOMDocument &$dom, &$txt, &$charNumber) {
		$spaces = array(' ', '~', "\n");
		if (isset($txt[$charNumber-1])) {
			$preChar = $txt[$charNumber-1];
		} else {
			$preChar = false;
		}
		if (isset($txt[$charNumber+1])) {
			$postChar = $txt[$charNumber+1];
		} else {
			$postChar = false;
		}
		$type = false;
		if (in_array($preChar, $spaces) && in_array($postChar, $spaces)) {
			$type = 'ndash';
		} elseif (false === $preChar && in_array($postChar, $spaces)) {
			$type = 'ndash';
		}
		if (false === $type) {
			$return = array($dom->createTextNode('-'));
		} else {
			$return = array($dom->createEntityReference($type));
		}
		return $return;
	}

	protected function addInlineTemp(DOMDocument &$dom, &$txt, &$charNumber, $param) {
		$charNumber += strlen($param);
		$html = $this->createElement($dom, 'ufra:html');
		$html->setAttribute('content', $this->temps[$param]);
		return array($html);
	}

	protected function addInlineNdash(DOMDocument &$dom, &$txt, &$charNumber) {
		$charNumber++;
		return array($dom->createEntityReference('ndash'));
	}

	protected function addInlineMdash(DOMDocument &$dom, &$txt, &$charNumber) {
		$charNumber+=2;
		return array($dom->createEntityReference('mdash'));
	}

	protected function addInlineHellip(DOMDocument &$dom, &$txt, &$charNumber) {
		$charNumber+=2;
		return array($dom->createEntityReference('hellip'));
	}

	protected function addInlineNoBreakSpace(DOMDocument &$dom, &$txt, &$charNumber) {
		return array($dom->createEntityReference('nbsp'));
	}

	protected function addInlineWordWrap(DOMDocument &$dom, &$txt, &$charNumber) {
		return array($dom->createEntityReference('shy'));
	}

	protected function addInlineQuotation(DOMDocument &$dom, &$txt, &$charNumber) {
		$return = array();
		$charNumber++;
		$content = $this->findInlineContent($txt, '"', $charNumber);
		if (false !== $content) {
			$return[] = $dom->createEntityReference('bdquo');
			$elements = $this->parseInline($dom, $content, self::TOKENS_INLINE);
			foreach ($elements as $element) {
				$return[] = $element;
			}
			$return[] = $dom->createEntityReference('rdquo');
		}
		return $return;
	}

	protected function addInlineLineBreak(DOMDocument &$dom, &$txt, &$charNumber) {
		$charNumber++;
		return array($this->createElement($dom, 'br'));
	}

	protected function addInlineText(DOMDocument &$dom, $txt) {
		return array($dom->createTextNode($txt));
	}

	protected function addInlineEscape(DOMDocument &$dom, &$txt, &$charNumber) {
		$charNumber++;
		if (isset($txt[$charNumber])) {
			return $this->addInlineText($dom, $txt[$charNumber]);
		} else  {
			return array();
		}
	}

	protected function addInlineHref(DOMDocument &$dom, &$txt, &$charNumber) {
		$charNumber+=2;
		$return = array();
		$content = $this->findInlineContent($txt, ']]', $charNumber);
		if (false !== $content) {
			$pos = 0;
			$aHref = $this->findInlineContent($content, ' ', $pos);
			$pos++;
			$aDesc = $this->findInlineContent($content, '|', $pos);
			$pos++;
			if ('' === $aDesc) {
				$aDesc = $aHref;
			}
			$aTitle = $this->findInlineContent($content, ']]', $pos);
			$a = $this->createElement($dom, 'a');
			if ('' !== $aTitle) {
				$a->setAttribute('title', $aTitle);
			}
			$elements = $this->parseInline($dom, $aDesc, self::TOKENS_INLINE_IN_HREF);
			$this->appendElements($a, $elements);
			$a->setAttribute('href', $aHref);
			$return[] = $a;
		}
		return $return;
	}

	protected function addInlineImage(DOMDocument &$dom, &$txt, &$charNumber) {
		$charNumber+=1;
		$return = array();
		$content = $this->findInlineContent($txt, ']', $charNumber);
		if (false !== $content) {
			$pos = 0;
			$imgSrc = $this->findInlineContent($content, ' ', $pos);
			$pos++;
			$imgAlt = $this->findInlineContent($content, '|', $pos);
			$pos++;
			$imgTitle = $this->findInlineContent($content, ']', $pos);
			$img = $this->createElement($dom, 'img');
			$img->setAttribute('src', $imgSrc);
			$img->setAttribute('alt', $imgAlt);
			if ('' !== $imgTitle) {
				$img->setAttribute('title', $imgTitle);
			}
			$return[] = $img;
		}
		return $return;
	}

	protected function addInlineSimplyElement(DOMDocument &$dom, &$txt, &$charNumber, $tag, $startToken, $endToken, $scope=self::TOKENS_INLINE) {
		$return = array();
		$charNumber+=strlen($startToken);
		$content = $this->findInlineContent($txt, $endToken, $charNumber);
		if (false !== $content) {
			$el = $this->createElement($dom, $tag);
			$elements = $this->parseInline($dom, $content, $scope);
			$this->appendElements($el, $elements);
			$return[] = $el;
		}
		return $return;
	}

	protected function addInlineStrong(DOMDocument &$dom, &$txt, &$charNumber) {
		return $this->addInlineSimplyElement($dom, $txt, $charNumber, 'strong', '**', '**');
	}

	protected function addInlineEmphasy(DOMDocument &$dom, &$txt, &$charNumber) {
		return $this->addInlineSimplyElement($dom, $txt, $charNumber, 'em', '*', '*');
	}

	protected function addInlineQuote(DOMDocument &$dom, &$txt, &$charNumber) {
		return $this->addInlineSimplyElement($dom, $txt, $charNumber, 'q', ',,', "''");
	}

	protected function addInlineSubscript(DOMDocument &$dom, &$txt, &$charNumber) {
		return $this->addInlineSimplyElement($dom, $txt, $charNumber, 'sub', '_', '_');
	}

	protected function addInlineSupscript(DOMDocument &$dom, &$txt, &$charNumber) {
		return $this->addInlineSimplyElement($dom, $txt, $charNumber, 'sup', '^', '^');
	}

	private function addParam($name, $value) {
		if (isset($this->params[$name])) {
			$this->params[$name][] = $value;
		} else {
			$this->params[$name] = array($value);
		}
	}

	protected function addBlockId(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$name = ltrim($lines[$lineNumber], '.');
		$name = strtolower(trim(UFlib_Strings::filter($name), '-'));
		if (in_array($name, $this->ids)) {
			for ($i=2; in_array($name.'-'.$i, $this->ids); ++$i) {
			}
			$name = $name.'-'.$i;
		}
		$this->addParam('id', $name);
		return array();
	}

	protected function addBlockClass(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$name = ltrim($lines[$lineNumber], '.');
		$this->addParam('class', $name);
		return array();
	}

	protected function addBlockListItems(DOMDocument &$dom, array &$lines) {
		$return = array();
		$linesCount = count($lines);
		$listTokens = $this->tokens[self::TOKENS_BLOCK_BLOCK_LISTS];
		for ($i=0; $i<$linesCount; $i++) {
			$li = $this->createElement($dom, 'li');
			$elements = $this->parseInline($dom, array($lines[$i]), self::TOKENS_INLINE);
			$this->appendElements($li, $elements);
			$aggregated = array();
			while (isset($lines[$i+1]) && isset($listTokens[$lines[$i+1][0]])) {
				$aggregated[] = $lines[++$i];
			}
			if (count($aggregated)) {
				$elements = $this->parseBlock($dom, $aggregated, self::TOKENS_BLOCK_BLOCK_LISTS);
				$this->appendElements($li, $elements);
			}
			$return[] = $li;
		}
		return $return;
	}

	protected function addBlockListUnordered(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$aggregated = array();
		while (isset($lines[$lineNumber][0]) && '*' === $lines[$lineNumber][0]) {
			$aggregated[] = trim(substr($lines[$lineNumber], 1));
			$lineNumber++;
		}
		$lineNumber--;
		$ul = $this->createElement($dom, 'ul');
		$elements = $this->addBlockListItems($dom, $aggregated);
		$this->appendElements($ul, $elements);
		$return = array($ul);
		return $return;
	}

	protected function addBlockListOrdered(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$aggregated = array();
		while (isset($lines[$lineNumber][0]) && '#' === $lines[$lineNumber][0]) {
			$aggregated[] = trim(substr($lines[$lineNumber], 1));
			$lineNumber++;
		}
		$lineNumber--;
		$ol = $this->createElement($dom, 'ol');
		$elements = $this->addBlockListItems($dom, $aggregated);
		$this->appendElements($ol, $elements);
		$return = array($ol);
		return $return;
	}

	protected function addBlockHr(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$return = array();
		$h = $this->createElement($dom, 'hr');
		$return[] = $h;
		return $return;
	}

	protected function addBlockHeader($level, DOMDocument &$dom, $line) {
		$return = array();
		if ($level > 6) {
			$this->addParam('class', 'h'.$level);
			$level = 6;
		}
		$tmp = trim(ltrim($line, '!'));
		if (!isset($this->params['id'])) {
			$t = strtolower(trim(UFlib_Strings::filter($tmp), '-'));
			if (in_array($t, $this->ids)) {
				for ($i=2; in_array($t.'-'.$i, $this->ids); ++$i) {
				}
				$t = $t.'-'.$i;
			}
			$this->addParam('id', $t);
		}
		$h = $this->createElement($dom, 'h'.$level);
		$elements = $this->parseInline($dom, $tmp, self::TOKENS_INLINE);
		$this->appendElements($h, $elements);
		$return[] = $h;
		return $return;
	}

	protected function addBlockHeader1(DOMDocument &$dom, array &$lines, &$lineNumber) {
		return $this->addBlockHeader(1+$this->level, $dom, $lines[$lineNumber]);
	}

	protected function addBlockHeader2(DOMDocument &$dom, array &$lines, &$lineNumber) {
		return $this->addBlockHeader(2+$this->level, $dom, $lines[$lineNumber]);
	}

	protected function addBlockHeader3(DOMDocument &$dom, array &$lines, &$lineNumber) {
		return $this->addBlockHeader(3+$this->level, $dom, $lines[$lineNumber]);
	}

	protected function addBlockHeader4(DOMDocument &$dom, array &$lines, &$lineNumber) {
		return $this->addBlockHeader(4+$this->level, $dom, $lines[$lineNumber]);
	}

	protected function addBlockHeader5(DOMDocument &$dom, array &$lines, &$lineNumber) {
		return $this->addBlockHeader(5+$this->level, $dom, $lines[$lineNumber]);
	}

	protected function addBlockHeader6(DOMDocument &$dom, array &$lines, &$lineNumber) {
		return $this->addBlockHeader(6+$this->level, $dom, $lines[$lineNumber]);
	}

	protected function addBlockParagraph(DOMDocument &$dom, array &$lines) {
		$return = array();
		$aggregated =& $lines;
		if (count($lines)) {
			$p = $this->createElement($dom, 'p');
			$elements = $this->parseInline($dom, $aggregated, self::TOKENS_INLINE);
			$this->appendElements($p, $elements);
			$return[] = $p;
		}
		return $return;
	}

	protected function addBlockBlockquote(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$return = array();
		$aggregated = array();
		while (isset($lines[$lineNumber][0]) && '>' === $lines[$lineNumber][0]) {
			$aggregated[] = (string)substr($lines[$lineNumber], 1);
			$lineNumber++;
		}
		$quote = $this->createElement($dom, 'blockquote');
		$elements = $this->parseBlock($dom, $aggregated, self::TOKENS_BLOCK);
		$this->appendElements($quote, $elements);
		$return[] = $quote;
		return $return;
	}

	protected function addBlockDiv(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$return = array();
		$aggregated = array();

		$div = $this->createElement($dom, 'div');
		$params = array();
		if (isset($lines[$lineNumber][1])) {
			if ('%' === $lines[$lineNumber][1]) {
				$val = substr($lines[$lineNumber], 2);
				if ($val) {
					$params['id'] = $val;
				}
			} else {
				$val = substr($lines[$lineNumber], 1);
				if ($val) {
					$params['class'] = $val;
				}
			}
		}
		foreach ($params as $key=>$val) {
			$div->setAttribute($key, $val);
		}
		$lineNumber++;

		$linesCount = count($lines);
		for ($i=&$lineNumber; $i<$linesCount; $i++) {
			if (isset($lines[$lineNumber][0]) && '%' === $lines[$lineNumber][0]) {
				if (isset($lines[$lineNumber][1]) && '/' === $lines[$lineNumber][1]) {
					break;
				} else {	// poczatek innego div-a
					$elements = $this->parseBlock($dom, $aggregated, self::TOKENS_BLOCK);
					$aggregated = array();
					$this->appendElements($div, $elements);
					$elements = $this->addBlockDiv($dom, $lines, $lineNumber);
					$this->appendElements($div, $elements);
				}
			} else {
				$aggregated[] = $lines[$lineNumber];
			}
		}
		$elements = $this->parseBlock($dom, $aggregated, self::TOKENS_BLOCK);
		$this->appendElements($div, $elements);

		$return[] = $div;
		return $return;
	}

	protected function addBlockVideoGoogle(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$return = array();
		$html = $this->createElement($dom, 'ufra:videoGoogle');
		$html->setAttribute('content', substr($lines[$lineNumber], 3));
		$return[] = $html;
		return $return;;
	}

	protected function addBlockVideoYoutube(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$return = array();
		$html = $this->createElement($dom, 'ufra:videoYoutube');
		$html->setAttribute('content', substr($lines[$lineNumber], 3));
		$return[] = $html;
		return $return;;
	}

	protected function addBlockVideoMetacafe(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$return = array();
		$html = $this->createElement($dom, 'ufra:videoMetacafe');
		$html->setAttribute('content', substr($lines[$lineNumber], 3));
		$return[] = $html;
		return $return;;
	}

	protected function addBlockFlash(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$return = array();
		list($width, $height, $url) = explode(':', substr($lines[$lineNumber], 3), 3);
		$html = $this->createElement($dom, 'ufra:flash');
		$html->setAttribute('content', $url);
		$html->setAttribute('width', $width);
		$html->setAttribute('height', $height);
		$return[] = $html;
		return $return;;
	}

	protected function addBlockHtml(DOMDocument &$dom, array &$lines, &$lineNumber) {
		$tmp = explode(':', substr($lines[$lineNumber], 3), 2);
		$name = $tmp[0];
		if (isset($tmp[1])) {	// jednolinijkowa definicja html
			$value = $tmp[1];
		} else {	// wielolinijkowa definicja html zakonczona "@/"
			$value = '';
			$linesCount = count($lines);
			$lineNumber++;
			for ($i=&$lineNumber; $i<$linesCount; $i++) {
				if ('@/' == $lines[$lineNumber]) {
					break;
				} else {
					$value .= "\n".$lines[$lineNumber];
				}
			}
		}
		$this->temps[$name] = $value;
		$this->fillTemp('$'.$name, 'Temp/'.$name);
		return array();
	}

	protected function fillTemp($name, $func) {
		$len = strlen($name);
		$temps =& $this->tokens[self::TOKENS_INLINE_TEMP];
		for ($i=1; $i<$len; $i++) {
			$tmp = substr($name, 0, $i);
			if (!isset($temps[$tmp])) {
				$temps[$tmp] = false;
			}
		}
		$temps[$name] = $func;
		$this->generateScopes();
	}

	protected function findToken($txt, array &$tokens, $charNumber=0) {
		$found = true;
		$type = false;
		$len = 1;
		$oldTmp = false;
		while ($found) {
			$tmp = substr($txt, $charNumber, $len);
			if ($oldTmp === $tmp) {
				break;
			}
			if (isset($tokens[$tmp])) {
				$type = $tokens[$tmp];
			} else {
				$found = false;
			}
			$oldTmp = $tmp;
			$len++;
		}
		return $type;
	}

	protected function findInlineContent(&$txt, $endToken, &$charNumber, $escape='\\') {
		$escapeLen = strlen($escape);
		$endTokenLen = strlen($endToken);
		$found = false;
		$start = $charNumber;
		if ($charNumber > strlen($txt)) {
			return '';
		}
		while (!$found) {
			$pos = strpos($txt, $endToken, $charNumber);
			if (false === $pos) {	// nic nie znaleziono
				$charNumber = strlen($txt);
				break;
			} elseif ($escape !== substr($txt, $pos-$escapeLen, $escapeLen)) {	// koniec nie jest escape'niety
				$charNumber = $pos;
				$found = true;
			} else {	// koniec escape'niety
				$charNumber = $pos + $endTokenLen;
			}
		}
		$return = substr($txt, $start, $charNumber-$start);
		$charNumber += $endTokenLen-1;
		return $return;
	}

	protected function appendElements(DOMElement &$parent, array &$children) {
		foreach ($children as $child) {
			$parent->appendChild($child);
		}
	}

	protected function createElement(DOMDocument &$dom, $name) {
		$el = $dom->createElement($name);
		foreach ($this->params as $type=>$params) {
			if ('id' === $type) {
				foreach ($params as $p) {
					$this->ids[] = $p;
				}
			}
			$el->setAttribute($type, implode(' ', $params));
		}
		$this->params = array();
		return $el;
	}
}
