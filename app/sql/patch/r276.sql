CREATE OR REPLACE RULE update_banned_off AS
    ON UPDATE TO computers_bans
   WHERE old.active = true AND new.active = false AND (( SELECT count(computers_bans.id) AS count
           FROM computers_bans
          WHERE computers_bans.active AND computers_bans.computer_id = old.computer_id)) < 2 DO  UPDATE computers SET banned = false
  WHERE computers.id = old.computer_id;

DROP INDEX fki_penalites_amnesty_by;

CREATE INDEX fki_penalties_amnesty_by
  ON penalties
  USING btree
  (amnesty_by);
