ALTER TABLE users ADD COLUMN last_login_at timestamp without time zone;
ALTER TABLE users ADD COLUMN last_login_ip inet;
ALTER TABLE admins ADD COLUMN password2 character(32);