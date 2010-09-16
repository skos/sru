DROP TABLE users_old;
DROP TABLE users_walet;

UPDATE text SET content = '
Musisz [[/sru zalogować się]], jeżeli chcesz zminić swoje dane.

! Rejestracja komputerów

Dokładny opis procesu rejestracji dostępny jest w [[http://skos.ds.pg.gda.pl/wiki/faq/rejestracja FAQ]]. Oto najważniejsze punkty:

* Zamelduj się w administracji Domu Studenckiego.
* Zaloguj się na konto danymi uzyskanymi w trakcie rejestracji.
** Podaj prawdziwe dane - bez tego będziesz mieć problem z dostępem do sieci. Będziemy kontaktować się z Tobą tylko i wyłącznie pocztą elektroniczną, więc podaj adres, który regularnie sprawdzasz.
** Pamiętaj, aby [[/sru/profile aktualizować dane]], jeżeli ulegną zmianie.
** Wypełnij formularz [[/sru/computers/:add rejestracji komputera]].
** Nazwa komputera określa, jak będzie on widoczny w sieci - jest prawie dowolna, ale może zawierać tylko litery, cyfry oraz myślnik.
** Jak odczytać adres MAC w systemach: [[http://skos.ds.pg.gda.pl/wiki/faq/rejestracja/win95 Windows (przed NT)]], [[http://skos.ds.pg.gda.pl/wiki/faq/rejestracja/winxp Windows (pozostałe)]], [[http://skos.ds.pg.gda.pl/wiki/faq/rejestracja/apple Mac OS]] i [[http://skos.ds.pg.gda.pl/wiki/faq/rejestracja/unix Unix/Linux]]. Więcej informacji w [[http://skos.ds.pg.gda.pl/wiki/faq/rejestracja FAQ]].
* Poczekaj cierpliwie, aż sieć zacznie działać. Może to potrwać nawet kilka godzin."
' WHERE id = 5;