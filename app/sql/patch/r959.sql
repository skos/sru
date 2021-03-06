-- Sequence: countries_id_seq

-- DROP SEQUENCE countries_id_seq;

CREATE SEQUENCE countries_id_seq
  INCREMENT 1
  MINVALUE 1
  NO MAXVALUE
  START 1
  CACHE 1;

-- Table: countries

-- DROP TABLE countries;

CREATE TABLE countries
(
  id bigserial NOT NULL,
  nationality varchar(50) NOT NULL, -- nazwa kraju
  CONSTRAINT countries_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE countries ADD CONSTRAINT countries_nationality_key UNIQUE ("nationality");
INSERT INTO countries VALUES (1, 'polska');

ALTER TABLE users 
	ADD COLUMN nationality bigint,
	ADD COLUMN address TEXT,
	ADD COLUMN birth_date TIMESTAMP WITHOUT TIME ZONE,
	ADD COLUMN birth_place character varying(100),
	ADD COLUMN pesel character(11),
	ADD COLUMN document_type smallint NOT NULL default 0,
	ADD COLUMN document_number character varying(20),
	ADD COLUMN user_phone_number character varying(20),
	ADD COLUMN guardian_phone_number character varying(20),
	ADD COLUMN sex BOOLEAN NOT NULL DEFAULT false;

ALTER TABLE users ADD CONSTRAINT users_nationality_id_fkey FOREIGN KEY (nationality) REFERENCES countries (id) ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE users ADD CONSTRAINT users_pesel_key UNIQUE (pesel);

ALTER TABLE users_history
	ADD COLUMN nationality bigint,
	ADD COLUMN address TEXT,
	ADD COLUMN birth_date TIMESTAMP WITHOUT TIME ZONE,
	ADD COLUMN birth_place TEXT,
	ADD COLUMN pesel TEXT,
	ADD COLUMN document_type TEXT,
	ADD COLUMN document_number TEXT,
	ADD COLUMN user_phone_number TEXT,
	ADD COLUMN guardian_phone_number TEXT,
	ADD COLUMN sex BOOLEAN;

ALTER TABLE users_history ADD CONSTRAINT users_history_nationality_id_fkey FOREIGN KEY (nationality) REFERENCES countries (id) ON UPDATE CASCADE ON DELETE NO ACTION;
	
CREATE OR REPLACE FUNCTION user_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.password!=OLD.password
then
	INSERT INTO users_history (
		user_id,
		name,
		surname,
		login,
		email,
		gg,
		faculty_id,
		study_year_id,
		location_id,
		modified_by,
		modified_at,
		comment,
		active,
		referral_start,
		referral_end,
		registry_no,
		services_available,
		update_needed,
		change_password_needed,
		password_changed,
		lang,
		type_id,
		nationality,
		address,
		birth_date,
		birth_place,
		pesel,
		document_type,
		document_number,
		user_phone_number,
		guardian_phone_number,
		sex
	) VALUES (
		OLD.id,
		OLD.name,
		OLD.surname,
		OLD.login,
		OLD.email,
		OLD.gg,
		OLD.faculty_id,
		OLD.study_year_id,
		OLD.location_id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.active,
		OLD.referral_start,
		OLD.referral_end,
		OLD.registry_no,
		OLD.services_available,
		OLD.update_needed,
		OLD.change_password_needed,
		now(),
		OLD.lang,
		OLD.type_id,
		OLD.nationality,
		OLD.address,
		OLD.birth_date,
		OLD.birth_place,
		OLD.pesel,
		OLD.document_type,
		OLD.document_number,
		OLD.user_phone_number,
		OLD.guardian_phone_number,
		OLD.sex
	);
elsif
	NEW.name!=OLD.name OR
	NEW.surname!=OLD.surname OR
	NEW.login!=OLD.login OR
	NEW.email!=OLD.email OR
	(OLD.email IS NULL AND NEW.email IS NOT NULL) OR
	NEW.gg!=OLD.gg OR
	NEW.faculty_id!=OLD.faculty_id OR
	NEW.study_year_id!=OLD.study_year_id OR
	NEW.location_id!=OLD.location_id OR
	NEW.comment!=OLD.comment OR
	NEW.active!=OLD.active OR
	NEW.referral_start!=OLD.referral_start OR
	(OLD.referral_start IS NULL AND NEW.referral_start IS NOT NULL) OR
	(OLD.referral_start IS NOT NULL AND NEW.referral_start IS NULL) OR
	NEW.referral_end!=OLD.referral_end OR
	(OLD.referral_end IS NULL AND NEW.referral_end IS NOT NULL) OR
	(OLD.referral_end IS NOT NULL AND NEW.referral_end IS NULL) OR
	NEW.registry_no!=OLD.registry_no OR
	(OLD.registry_no IS NULL AND NEW.registry_no IS NOT NULL) OR
	(OLD.registry_no IS NOT NULL AND NEW.registry_no IS NULL) OR
	NEW.services_available!=OLD.services_available OR
	NEW.update_needed!=OLD.update_needed OR
	NEW.change_password_needed!=OLD.change_password_needed OR
	NEW.lang!=OLD.lang OR
	NEW.type_id!=OLD.type_id OR
	NEW.nationality!=OLD.nationality OR
	(OLD.nationality IS NULL AND NEW.nationality IS NOT NULL) OR
	(OLD.nationality IS NOT NULL AND NEW.nationality IS NULL) OR
	NEW.address!=OLD.address OR
	(OLD.address IS NULL AND NEW.address IS NOT NULL) OR
	(OLD.address IS NOT NULL AND NEW.address IS NULL) OR
	NEW.birth_date!=OLD.birth_date OR
	(OLD.birth_date IS NULL AND NEW.birth_date IS NOT NULL) OR
	(OLD.birth_date IS NOT NULL AND NEW.birth_date IS NULL) OR
	NEW.birth_place!=OLD.birth_place OR
	(OLD.birth_place IS NULL AND NEW.birth_place IS NOT NULL) OR
	(OLD.birth_place IS NOT NULL AND NEW.birth_place IS NULL) OR
	NEW.pesel!=OLD.pesel OR
	(OLD.pesel IS NULL AND NEW.pesel IS NOT NULL) OR
	(OLD.pesel IS NOT NULL AND NEW.pesel IS NULL) OR
	NEW.document_type!=OLD.document_type OR
	NEW.document_number!=OLD.document_number OR
	(OLD.document_number IS NULL AND NEW.document_number IS NOT NULL) OR
	(OLD.document_number IS NOT NULL AND NEW.document_number IS NULL) OR
	NEW.user_phone_number!=OLD.user_phone_number OR
	(OLD.user_phone_number IS NULL AND NEW.user_phone_number IS NOT NULL) OR
	(OLD.user_phone_number IS NOT NULL AND NEW.user_phone_number IS NULL) OR
	NEW.guardian_phone_number!=OLD.guardian_phone_number OR
	(OLD.guardian_phone_number IS NULL AND NEW.guardian_phone_number IS NOT NULL) OR
	(OLD.guardian_phone_number IS NOT NULL AND NEW.guardian_phone_number IS NULL) OR
	NEW.sex!=OLD.sex
then
	INSERT INTO users_history (
		user_id,
		name,
		surname,
		login,
		email,
		gg,
		faculty_id,
		study_year_id,
		location_id,
		modified_by,
		modified_at,
		comment,
		active,
		referral_start,
		referral_end,
		registry_no,
		services_available,
		update_needed,
		change_password_needed,
		lang,
		type_id,
		nationality,
		address,
		birth_date,
		birth_place,
		pesel,
		document_type,
		document_number,
		user_phone_number,
		guardian_phone_number,
		sex
	) VALUES (
		OLD.id,
		OLD.name,
		OLD.surname,
		OLD.login,
		OLD.email,
		OLD.gg,
		OLD.faculty_id,
		OLD.study_year_id,
		OLD.location_id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.active,
		OLD.referral_start,
		OLD.referral_end,
		OLD.registry_no,
		OLD.services_available,
		OLD.update_needed,
		OLD.change_password_needed,
		OLD.lang,
		OLD.type_id,
		OLD.nationality,
		OLD.address,
		OLD.birth_date,
		OLD.birth_place,
		OLD.pesel,
		OLD.document_type,
		OLD.document_number,
		OLD.user_phone_number,
		OLD.guardian_phone_number,
		OLD.sex
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
COMMENT ON FUNCTION user_update() IS 'archiwizacja danych uzytkownika';