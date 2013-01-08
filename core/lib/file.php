<?php
/**
 * operacje plikowe
 */
class UFlib_File {

	/**
	 * zmienia wymiary obrazka
	 * 
	 * @param string $src - sciezka do pliku zrodlowego
	 * @param int $width - nowa szerokosc
	 * @param int $height - nowa wysokosc
	 * @param string/null $dst - sciezka do pliku wynikowego; jezeli nie NULL,
	 *                           to oryginalny plik zostanie nadpisany
	 *                           przeskalowana wersja
	 * @param int/null x - wspolrzedna x-owa punktu, ktory musi byc zawarty w obrazie wynikowym;
	 *                     liczba ujemna to polozenie w procentach od lewej strony;
	 *                     liczba dodatnia to polozenie w pikselach od gory;
	 *                     null - przeskalowanie z zachowaniem proporcji
	 * @param int/null y - wspolrzedna y-owa punktu, ktory musi byc zawarty w obrazie wynikowym;
	 *                     liczba ujemna to polozenie w procentarch od gory;
	 *                     liczba dodatnia to polozenie w pikselach od gory;
	 *                     null - przeskalowanie z zachowaniem proporcji
	 * @param bool enlarge - czy obraz moze byc powiekszony?
	 * @return bool - czy udalo sie przeskalowac
	 */
	static public function imageResize($src, $width, $height, $dst=null, $x=null, $y=null, $enlarge=false) {
		$srcOrig = $src;
		$src = escapeshellarg($src);
		$width = (int)$width;
		$height = (int)$height;

		if (is_int($x) && is_int($y)) {
			$params = self::imageParameters($srcOrig);

			$widthOrig = $params['width'];
			$heightOrig = $params['height'];

			if ($x<0) {
				$x = floor($widthOrig * (-1) * $x / 100);
			}
			if ($y<0) {
				$y = floor($heightOrig * (-1) * $y / 100);
			}
				
			if ($width/$height > $widthOrig/$heightOrig) {	// wyciecie mniej wydluzone niz obrazek

				$w = $widthOrig;
				$h = floor($widthOrig * $height / $width);

				$offsetX = 0;
				if ($y < $h/2) {	// punkt blisko gornej krawedzi
					$offsetY = 0;
				} elseif ($y > ($heightOrig-$h/2)) {	// punkt blisko dolnej krawedzi
					$offsetY = $heightOrig - $h;
				} else {	// punkt gdzies posrodku
					$offsetY = $y - floor($h / 2);
				}

			} else {	// wyciecie bardziej wydluzone niz obrazek

				$h = $heightOrig;
				$w = floor($heightOrig * $width / $height);

				$offsetY = 0;
				if ($x < $w/2) {
					$offsetX = 0;
				} elseif ($x > ($widthOrig-$w/2)) {
					$offsetX = $widthOrig - $w;
				} else {
					$offsetX = $x - floor($w / 2);
				}
			}
			$args = '-crop '.$w.'x'.$h.'+'.$offsetX.'+'.$offsetY.' -thumbnail "'.$width.'x'.$height.'!'.($enlarge?'':'>').'" -unsharp 0.5x0.5+1.2+0.05';
		} else {
			$args = '-thumbnail "'.$width.'x'.$height.($enlarge?'':'>').'" -unsharp 0.5x0.5+1.2+0.05';
		}

		if (is_string($dst)) {
			$dst = escapeshellarg($dst);
			$command = 'convert '.$src.' '.$args.' '.$dst;
		} else {
			$command = 'mogrify '.$args.' '.$src;
		}
		exec($command, $output, $exitCode);
		if ($exitCode) {	// wystapil jakis blad
			throw UFra::factory('UFex_Lib_BadResult', 'Image '.$src.' not resized'.(is_string($dst)?' into '.$dst:''), null, null, $output);
		}
	}

	/**
	 * parametry obrazu
	 * 
	 * @param string $file - sciezka do pliku
	 * @return array[width, height, mime] - parametry
	 */
	static public function imageParameters($file) {
		$params = getimagesize($file);
		if (!is_array($params)) {
			throw UFra::factory('UFex_Lib_BadResult', 'Could not get image "'.$file.'" properities');
		}
		return array(
			'width'  => (int)$params[0],
			'height' => (int)$params[1],
			'mime'   => $params['mime'],
		);
	}

	/**
	 * tworzy katalog
	 *
	 * jezeli jest taka potrzeba, to tworzona jest rowniez cala sciezka do
	 * danego katalogu
	 * 
	 * @param string $dir - nazwa katalogu
	 * @param int $perm - uprawnienia
	 * @return bool - utworzono (TRUE), katalog istnial (FALSE)
	 */
	static public function dirMake($dir, $perm=0775) {
		if (is_dir($dir)) {
			return false;
		}
		if (!mkdir($dir, $perm, true)) {
			throw UFra::factory('UFex_Lib_BadResult', 'Dir "'.$dir.'" not created');
		}
		return true;
	}

	/**
	 * kasuje plik
	 * 
	 * @param string $file - nazwa pliku do skasowania
	 */
	static public function fileDelete($file) {
		if (!unlink($file)) {
			throw UFra::factory('UFex_Lib_BadResult', 'File "'.$file.'" not deleted');
		}
	}

	/**
	 * przenosi/zmienia nazwe pliku
	 *
	 * katalog na plik docelowy musi byc utworzony wczesniej
	 * 
	 * @param string $src - nazwa pliku zrodlowego
	 * @param string $dst - nazwa pliku docelowego
	 * @return bool - czy udalo sie przeniesc plik?
	 */
	static public function fileMove($src, $dst) {
		if (!rename($src, $dst)) {
			throw UFra::factory('UFex_Lib_BadResult', 'File "'.$src.'" not moved');
		}
		return true;
	}

	/**
	 * przenosi/zmienia nazwe pliku, ktory zostal uploadowany
	 *
	 * plik zrodlowy jest sprawdzany, czy zostal zuploadowany
	 * katalog na plik docelowy musi byc utworzony wczesniej
	 * 
	 * @param string $src - nazwa pliku zrodlowego
	 * @param string $dst - nazwa pliku docelowego
	 * @return bool - czy udalo sie przeniesc plik?
	 */
	static public function fileMoveUploaded($src, $dst) {
		if (!move_uploaded_file($src, $dst)) {
			throw UFra::factory('UFex_Lib_BadResult', 'File "'.$src.'" not moved or is not uploaded file');
		}
		return true;
	}
}
