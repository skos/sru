CREATE TABLE users_functions (
id bigserial NOT NULL,
user_id bigint NOT NULL,
function_id smallint NOT NULL,
dormitory_id bigint,
comment varchar(64),

CONSTRAINT users_functions_pkey PRIMARY KEY (id),
CONSTRAINT users_functions_user_id_fkey FOREIGN KEY (user_id)
REFERENCES users (id)
ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT users_functions_dormitory_id_fkey FOREIGN KEY (dormitory_id)
REFERENCES dormitories (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
COMMENT ON TABLE users_functions IS 'funkcje uzytkownikow na rzecz DS i Osiedla';

CREATE UNIQUE INDEX users_functions_user_function_key
  ON users_functions
  USING btree
  (user_id, function_id);