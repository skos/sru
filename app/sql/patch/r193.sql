ALTER TABLE penalties RENAME admin_id  TO created_by;

ALTER TABLE penalties
   ALTER COLUMN modified_at DROP NOT NULL;
ALTER TABLE penalties
   ALTER COLUMN modified_at DROP DEFAULT;

ALTER TABLE penalties DROP CONSTRAINT bans_pkey;

ALTER TABLE penalties
  ADD CONSTRAINT penalties_pkey PRIMARY KEY(id);

ALTER TABLE penalties ADD FOREIGN KEY (created_by) REFERENCES admins (id)
   ON UPDATE CASCADE ON DELETE RESTRICT;
CREATE INDEX fki_penalties_created_by ON penalties(created_by);

ALTER TABLE penalties ADD FOREIGN KEY (modified_by) REFERENCES admins (id)
   ON UPDATE CASCADE ON DELETE RESTRICT;
CREATE INDEX fki_penalties_modified_by ON penalties(modified_by);

ALTER TABLE penalties ADD FOREIGN KEY (amnesty_by) REFERENCES admins (id)
   ON UPDATE CASCADE ON DELETE RESTRICT;
CREATE INDEX fki_penalites_amnesty_by ON penalties(amnesty_by);

ALTER TABLE penalties ADD FOREIGN KEY (user_id) REFERENCES users (id)
   ON UPDATE CASCADE ON DELETE CASCADE;
CREATE INDEX fki_penalties_user_id ON penalties(user_id);

ALTER TABLE users
   ADD COLUMN banned boolean DEFAULT false;

UPDATE users SET banned=false;

ALTER TABLE users
   ALTER COLUMN banned SET NOT NULL;

ALTER TABLE penalties
   ADD COLUMN active boolean DEFAULT true;

update penalties set active=true;

ALTER TABLE penalties
   ALTER COLUMN active SET NOT NULL;

ALTER TABLE computers_bans ADD FOREIGN KEY (computer_id) REFERENCES computers (id)
   ON UPDATE CASCADE ON DELETE CASCADE;
CREATE INDEX fki_computers_bans_computer_id ON computers_bans(computer_id);

ALTER TABLE computers_bans ADD FOREIGN KEY (penalty_id) REFERENCES penalties (id)
   ON UPDATE CASCADE ON DELETE CASCADE;
CREATE INDEX fki_computers_bans_penalty_id ON computers_bans(penalty_id);

COMMENT ON COLUMN users.banned IS 'czy uzytkownik jest w tej chwili zabanowany?';

ALTER TABLE computers_bans
   ADD COLUMN active boolean DEFAULT true;

update computers_bans set active=true;

ALTER TABLE computers_bans
   ALTER COLUMN active SET NOT NULL;

CREATE OR REPLACE RULE insert_banned_on AS
    ON INSERT TO penalties
  WHERE new.type_id!=1
  DO  UPDATE users SET banned = true
  WHERE users.id = new.user_id;

CREATE OR REPLACE RULE insert_counter AS
    ON INSERT TO penalties
  do UPDATE users SET bans = (users.bans + 1)
  WHERE users.id = new.user_id;

CREATE OR REPLACE RULE insert_banned_on AS
   ON INSERT TO computers_bans
   DO 
update computers set banned=true where computers.id=new.computer_id;

CREATE OR REPLACE RULE insert_counter AS
   ON INSERT TO computers_bans
   DO 
update computers set bans=bans+1 where computers.id=new.computer_id;

CREATE OR REPLACE RULE update_banned_off AS
   ON UPDATE TO penalties
   WHERE old.active=true and new.active=false
   DO 
update users set banned=false where users.id=old.user_id;

CREATE OR REPLACE RULE update_computers_bans_off AS
    ON UPDATE TO penalties
   WHERE old.active=true and new.active = false DO  UPDATE computers_bans SET active = false
  WHERE computers_bans.penalty_id = old.id;

CREATE OR REPLACE RULE update_banned_off AS
   ON UPDATE TO computers_bans
   WHERE old.active=true and new.active=false
   DO 
update computers set banned=false where computers.id=old.computer_id;
