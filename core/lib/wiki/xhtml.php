<?php

class UFlib_Wiki_Xhtml {
	
	protected $renderer;

	public function __construct() {
		$this->renderer = $this->chooseRenderer();
	}

	protected function chooseRenderer() {
		return UFra::shared('UFlib_Wiki');
	}

	protected function html($matches) {
		$txt = htmlspecialchars_decode($matches[1]);
		return str_replace('&#10;', "\n", $txt);
	}
	
	protected function videoGoogle($matches) {
		$matches[1] = 'http://video.google.com/googleplayer.swf?docId='.$matches[1];
		$matches[2] = 400;
		$matches[3] = 300;
		return $this->flash($matches);
	}
	
	protected function videoMetacafe($matches) {
		$matches[1] = 'http://lads.myspace.com/videos/vplayer.swf?m='.$matches[1].'&amp;type=video&amp;a=0';
		$matches[2] = 400;
		$matches[3] = 300;
		return $this->flash($matches);
	}
	
	protected function videoYoutube($matches) {
		$matches[1] = 'http://www.youtube.com/v/'.$matches[1];
		$matches[2] = 400;
		$matches[3] = 300;
		return $this->flash($matches);
	}

	protected function flash($matches) {
		$url = htmlspecialchars_decode($matches[1]);
		$width = $matches[2];
		$height = $matches[3];
		return '<object type="application/x-shockwave-flash" data="'.$url.'" width="'.$width.'" height="'.$height.'">'.
			'<param name="movie" value="'.$url.'" />'.
			'</object>';
	}
	
	protected function generateXhtml($txt, $scope, $level) {
		$dom = $this->renderer->render($txt, $scope, $level);
		$txt = $dom->saveXML();
		list($tmp, $txt) = explode("\n", $txt, 2);
		$txt = preg_replace_callback('/\<ufra:html content="(.*)"\/\>/U', array($this, 'html'), $txt);
		$txt = preg_replace_callback('/\<ufra:videoGoogle content="(.*)"\/\>/U', array($this, 'videoGoogle'), $txt);
		$txt = preg_replace_callback('/\<ufra:videoMetacafe content="(.*)"\/\>/U', array($this, 'videoMetacafe'), $txt);
		$txt = preg_replace_callback('/\<ufra:videoYoutube content="(.*)"\/\>/U', array($this, 'videoYoutube'), $txt);
		$txt = preg_replace_callback('/\<ufra:flash content="(.*)" width="(.*)" height="(.*)"\/\>/U', array($this, 'flash'), $txt);
		return $txt;
	}

	public function render($txt, $level=0) {
		return $this->generateXhtml($txt, UFlib_Wiki::TOKENS_BLOCK, $level);
	}

	public function renderInline($txt) {
		return $this->generateXhtml($txt, UFlib_Wiki::TOKENS_INLINE);
	}
}
