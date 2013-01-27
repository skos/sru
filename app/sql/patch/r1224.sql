ALTER TABLE admins ADD COLUMN password_inner character(32);
COMMENT ON COLUMN admins.password_inner IS 'haslo zakodowane MD5';
ALTER TABLE admins ADD COLUMN last_psw_inner_change timestamp without time zone;
UPDATE admins SET password_inner = password WHERE active = true AND type_id <= 4;
ALTER TABLE admins ALTER COLUMN password TYPE character(60);
COMMENT ON COLUMN admins.password IS 'haslo zakodowane blowfish';
UPDATE admins SET password = '-';
UPDATE admins SET password = password_blow WHERE password_blow IS NOT NULL;
ALTER TABLE admins DROP COLUMN password_blow;