ALTER TABLE penalties_history DROP CONSTRAINT penalties_history_penalty_id_fkey;
ALTER TABLE penalties_history ADD CONSTRAINT penalties_history_penalty_id_fkey FOREIGN KEY (penalty_id)
      REFERENCES penalties (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;