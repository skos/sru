ALTER TABLE users ADD COLUMN change_password_needed boolean NOT NULL DEFAULT false;
COMMENT ON COLUMN users.change_password_needed IS 'haslo wymaga zmiany?';