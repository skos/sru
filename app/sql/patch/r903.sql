ALTER TABLE users ADD COLUMN last_inv_login_at timestamp without time zone;
ALTER TABLE users ADD COLUMN last_inv_login_ip inet;

ALTER TABLE admins ADD COLUMN last_inv_login_at timestamp without time zone;
ALTER TABLE admins ADD COLUMN last_inv_login_ip inet;