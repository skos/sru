UPDATE users SET sex = false, modified_by = 28, modified_at = now() WHERE SUBSTRING(name FROM '.$') != 'a';
UPDATE users SET sex = true, modified_by = 28, modified_at = now() WHERE SUBSTRING(name FROM '.$') = 'a';
ALTER TABLE users ALTER COLUMN sex SET DEFAULT false;
ALTER TABLE users ALTER COLUMN sex SET NOT NULL;