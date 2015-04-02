ALTER TABLE users DROP CONSTRAINT users_faculty_id_fkey;
ALTER TABLE users DROP CONSTRAINT users_location_id_fkey;
ALTER TABLE users DROP CONSTRAINT users_modified_by_fkey;
ALTER TABLE users
  ADD CONSTRAINT users_faculty_id_fkey FOREIGN KEY (faculty_id)
      REFERENCES faculties (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE users
  ADD CONSTRAINT users_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES locations (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE users
  ADD CONSTRAINT users_modified_by_fkey FOREIGN KEY (modified_by)
      REFERENCES admins (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE users_history DROP CONSTRAINT users_history_faculty_id_fkey;
ALTER TABLE users_history DROP CONSTRAINT users_history_location_id_fkey;
ALTER TABLE users_history DROP CONSTRAINT users_history_modified_by_fkey;
ALTER TABLE users_history DROP CONSTRAINT users_history_nationality_id_fkey;
ALTER TABLE users_history
  ADD CONSTRAINT users_history_faculty_id_fkey FOREIGN KEY (faculty_id)
      REFERENCES faculties (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE users_history
  ADD CONSTRAINT users_history_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES locations (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE users_history
  ADD CONSTRAINT users_history_modified_by_fkey FOREIGN KEY (modified_by)
      REFERENCES admins (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE users_history
  ADD CONSTRAINT users_history_nationality_id_fkey FOREIGN KEY (nationality)
      REFERENCES countries (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE switches_port DROP CONSTRAINT switches_port_connected_switch_fkey;
ALTER TABLE switches_port DROP CONSTRAINT switches_port_location_fkey;
ALTER TABLE switches_port DROP CONSTRAINT switches_port_switch_fkey;
ALTER TABLE switches_port
  ADD CONSTRAINT switches_port_connected_switch_fkey FOREIGN KEY (connected_switch)
      REFERENCES switches (id)
      ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE switches_port
  ADD CONSTRAINT switches_port_location_fkey FOREIGN KEY (location)
      REFERENCES locations (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE switches_port
  ADD CONSTRAINT switches_port_switch_fkey FOREIGN KEY (switch)
      REFERENCES switches (id)
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE switches DROP CONSTRAINT switches_ipv4_fkey;
ALTER TABLE switches DROP CONSTRAINT switches_location_id_fkey;
ALTER TABLE switches DROP CONSTRAINT switches_model_fkey;
ALTER TABLE switches
  ADD CONSTRAINT switches_ipv4_fkey FOREIGN KEY (ipv4)
      REFERENCES ipv4s (ip)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE switches
  ADD CONSTRAINT switches_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES locations (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE switches
  ADD CONSTRAINT switches_model_fkey FOREIGN KEY (model)
      REFERENCES switches_model (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE penalties DROP CONSTRAINT penalties_user_id_fkey;
ALTER TABLE penalties
  ADD CONSTRAINT penalties_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES users (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE locations DROP CONSTRAINT locations_dormitory_id_fkey;
ALTER TABLE locations
  ADD CONSTRAINT locations_dormitory_id_fkey FOREIGN KEY (dormitory_id)
      REFERENCES dormitories (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE locations_history DROP CONSTRAINT locations_history_location_id_fkey;
ALTER TABLE locations_history
  ADD CONSTRAINT locations_history_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES locations (id)
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ipv4s DROP CONSTRAINT ipv4s_dormitory_id_fkey;
ALTER TABLE ipv4s
  ADD CONSTRAINT ipv4s_dormitory_id_fkey FOREIGN KEY (dormitory_id)
      REFERENCES dormitories (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE admins DROP CONSTRAINT admins_dormitory_id_fkey;
ALTER TABLE admins
  ADD CONSTRAINT admins_dormitory_id_fkey FOREIGN KEY (dormitory_id)
      REFERENCES dormitories (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE admins_dormitories DROP CONSTRAINT admins_dormitories_admin_id;
ALTER TABLE admins_dormitories DROP CONSTRAINT admins_dormitories_dormitory_id_fkey;
ALTER TABLE admins_dormitories
  ADD CONSTRAINT admins_dormitories_admin_id FOREIGN KEY (admin)
      REFERENCES admins (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE admins_dormitories
  ADD CONSTRAINT admins_dormitories_dormitory_id_fkey FOREIGN KEY (dormitory)
      REFERENCES dormitories (id)
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE admins_history DROP CONSTRAINT admins_history_admin_id_fkey;
ALTER TABLE admins_history
  ADD CONSTRAINT admins_history_admin_id_fkey FOREIGN KEY (admin_id)
      REFERENCES admins (id)
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE computers DROP CONSTRAINT computers_ipv4_fkey;
ALTER TABLE computers DROP CONSTRAINT computers_location_id_fkey;
ALTER TABLE computers DROP CONSTRAINT computers_user_id_fkey;
ALTER TABLE computers
  ADD CONSTRAINT computers_ipv4_fkey FOREIGN KEY (ipv4)
      REFERENCES ipv4s (ip)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE computers
  ADD CONSTRAINT computers_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES locations (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE computers
  ADD CONSTRAINT computers_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES users (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE computers_history DROP CONSTRAINT computers_history_location_id_fkey;
ALTER TABLE computers_history DROP CONSTRAINT computers_history_modified_by_fkey;
ALTER TABLE computers_history DROP CONSTRAINT computers_history_user_id_fkey;
ALTER TABLE computers_history
  ADD CONSTRAINT computers_history_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES locations (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE computers_history
  ADD CONSTRAINT computers_history_modified_by_fkey FOREIGN KEY (modified_by)
      REFERENCES admins (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE computers_history
  ADD CONSTRAINT computers_history_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES users (id)
      ON UPDATE CASCADE ON DELETE RESTRICT;
