<?php
/**
 * generator kodu html - rozne pomocnicze funkcje
 */
class UFtpl_Html {

	/**
	 * komunikat bledu
	 * 
	 * @param string $txt - tekst komunikatu
	 * @return string
	 */
	public static function msgErr($txt) {
		return '<p class="msgError">'.$txt.'</p>';
	}

	/**
	 * komunikat potwierdzajacy udana akcje
	 * 
	 * @param string $txt - tekst komunikatu
	 * @return string
	 */
	public static function msgOk($txt) {
		return '<p class="msgOk">'.$txt.'</p>';
	}

	/**
	 * panel nawigacyjny
	 * 
	 * @param string $urlPrefix - czesc url-a przed numerem strony
	 * @param int $page - aktualny numer strony
	 * @param bool $prev - czy jest poprzednia strona?
	 * @param bool $next - czy jest nastepna strona?
	 * @param string $txtPrev - tekst przycisku przechodzenia do poprzedniej strony
	 * @param string $txtNext - tekst przycisku przechodzenia do nastepnej strony
	 * @param string $urlSuffix - czesc url-a po numerze strony
	 * @param int $pages - calkowita ilosc stron
	 * @param int $gapMargin - ile linkow ma byc dookola aktywnej strony
	 * @return string - html panelu nawigacyjnego
	 */
	static public function navigation($urlPrefix, $page, $prev, $next, $txtPrev='Poprzedni', $txtNext='Następny', $urlSuffix='', $pages=null, $gapMargin=2) {
		$pager = '<p class="nav">';
		if ($prev) {
			$pager .= '<a class="prev" rel="prev" href="'.$urlPrefix.($page-1).$urlSuffix.'">'.$txtPrev.'</a>';
		} else {
			$pager .= '<span class="prev">'.$txtPrev.'</span>';
		}
		$pager .= ' ';
		if (is_int($pages)) {
			// wyliczenie przerw w numeracji
			$gap1start = 1;
			$gap1stop = $page-$gapMargin;
			$gap2start = $page+$gapMargin;
			$gap2stop = $pages;
			$gap = false;

			for ($i=1; $i<=$pages; $i++) {
				if (($i>$gap1start && $i<$gap1stop) || ($i>$gap2start && $i<$gap2stop)) {
					if (!$gap) {
						$pager .= '<span>&hellip;</span>';
						$gap = true;
					}
					continue;
				}
				if ($i == $page) {
					$pager .= '<strong>'.$i.'</strong> ';
				} else {
					$pager .= '<a href="'.$urlPrefix.$i.$urlSuffix.'">'.$i.'</a> ';
				}
				$gap = false;
			}
		}
		if ($next) {
			$pager .= '<a class="next" rel="next" href="'.$urlPrefix.($page+1).$urlSuffix.'">'.$txtNext.'</a>';
		} else {
			$pager .= '<span class="next">'.$txtNext.'</span>';
		}
		$pager .= '</p>';
		return $pager;
	}
}
