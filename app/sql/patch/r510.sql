ALTER TABLE penalty_templates ADD COLUMN reason_en text NOT NULL DEFAULT ''::text;

UPDATE penalty_templates set reason='Tworzenie podsieci w sieci (NAT). Skontaktuj się ze swoim administratorem lokalnym w celu złożenia wyjaśnień.' where id=1;
UPDATE penalty_templates set reason_en='Creating subnetworks (NAT). Contact your local administrator for explanations.' where id=1;
UPDATE penalty_templates set reason='Podszywanie się pod innych użytkowników w sieci.' where id=2;
UPDATE penalty_templates set reason_en='Impersonating other users in the SKOS network.' where id=2;
UPDATE penalty_templates set reason='Przekroczenie uploadu. Limituj ruch wychodzący poza SKOS do 60kB/s w godzinach od 10 rano do 1 w nocy, w przeciwnym przypadku zostaniesz ukarany odcięciem od usługi na 1 miesiąc.' where id=3;
UPDATE penalty_templates set reason_en='Too high upload. You should limit your outgoing traffic outside the SKOS network to 60kB/s between 10:00 A.M. - 1:00 A.M otherwise we cut Your access to the network for 1 month.' where id=3;
UPDATE penalty_templates set reason='Niezastosowanie się do wcześniejszych ostrzeżeń odnośnie ograniczenia uploadu.' where id=4;
UPDATE penalty_templates set reason_en='Not following to earlier requests about limiting the upload.' where id=4;
UPDATE penalty_templates set reason='Twój komputer jest najprawdopodobniej zawirusowany. Usuń problem.' where id=5;
UPDATE penalty_templates set reason_en='Your computer is probably infected by a virus. Please remove that problem.' where id=5;
UPDATE penalty_templates set reason='Komputer zawirusowany ponownie. Usuń problem.' where id=6;
UPDATE penalty_templates set reason_en='Your computer is infected by a virus again. Remove that problem.' where id=6;
UPDATE penalty_templates set reason='Udostępnianie pirackich kopii materiałów chronionych prawem autorskim.' where id=7;
UPDATE penalty_templates set reason_en='Sharing unauthorized copies of the materials protected by author''s law.' where id=7;
UPDATE penalty_templates set reason='Ponowne udostępnianie pirackich kopii materiałów chronionych prawem autorskim.' where id=8;
UPDATE penalty_templates set reason_en='Sharing unauthorized copies of the materials protected by author''s law again.' where id=8;
UPDATE penalty_templates set reason='Twoje dane w SRU są nieaktualne bądź niepoprawne. Zaloguj się na swoje konto w celu ich poprawienia, inaczej stracisz dostęp do Internetu.' where id=9;
UPDATE penalty_templates set reason_en='Your personal data in SRU is incorrect. Log into your account and correct it, otherwise your Internet access will be cut.' where id=9;
UPDATE penalty_templates set reason='Ponowne tworzenie podsieci w sieci (NAT). Skontaktuj się ze swoim administratorem lokalnym w celu złożenia wyjaśnień.' where id=10;
UPDATE penalty_templates set reason_en='Creating subnetworks (NAT) again. Please contact your local network administrator for explanation.' where id=10;
UPDATE penalty_templates set reason='Komputer zawirusowany ponownie. Usuń problem.' where id=11;
UPDATE penalty_templates set reason_en='Your computer is infected by a virus again.' where id=11;
UPDATE penalty_templates set reason='Ponowne podszywanie się pod innych użytkowników w sieci.' where id=12;
UPDATE penalty_templates set reason_en='Impersonating other users in the SKOS network again.' where id=12;
UPDATE penalty_templates set reason='Udostępnianie adresu MAC.' where id=13;
UPDATE penalty_templates set reason_en='Making available own MAC address.' where id=13;
UPDATE penalty_templates set reason='Ponowne udostępnianie pirackich kopii materiałów chronionych prawem autorskim.' where id=14;
UPDATE penalty_templates set reason_en='Sharing unauthorized copies of the materials protected by author''s law again.' where id=14;
UPDATE penalty_templates set reason='Ponownie informujemy, że Twoje dane w SRU są nieaktualne bądź niepoprawne. Zaloguj się na swoje konto w celu ich poprawienia, inaczej Twoje konto zostanie deaktywowane.' where id=16;
UPDATE penalty_templates set reason_en='Your personal data in SRU is still incorrect. Log into your account and correct it, otherwise your account will be dectivated.' where id=16;
UPDATE penalty_templates set reason='Uruchamianie usług serwerowych w sieci SKOS PG.' where id=20;
UPDATE penalty_templates set reason_en='Running server services in the SKOS network.' where id=20;
UPDATE penalty_templates set reason='Ponowne uruchamianie usług serwerowych w sieci SKOS PG.' where id=21;
UPDATE penalty_templates set reason_en='Running server services in the SKOS network again.' where id=21;
UPDATE penalty_templates set reason='Ponowne uruchamianie usług serwerowych w sieci SKOS PG.' where id=22;
UPDATE penalty_templates set reason_en='Running server services in the SKOS network again.' where id=22;