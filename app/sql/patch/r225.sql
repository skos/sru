ALTER TABLE dormitories
   ADD COLUMN users_max integer DEFAULT 0;

ALTER TABLE dormitories
   ADD COLUMN computers_max integer DEFAULT 0;

UPDATE dormitories set computers_max=0, users_max=0;

ALTER TABLE dormitories
   ALTER COLUMN users_max SET NOT NULL;

ALTER TABLE dormitories
   ALTER COLUMN computers_max SET NOT NULL;

update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=1), computers_max = (select count(*) from ipv4s where dormitory_id=1) where id=1;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=2), computers_max = (select count(*) from ipv4s where dormitory_id=2) where id=2;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=3), computers_max = (select count(*) from ipv4s where dormitory_id=3) where id=3;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=4), computers_max = (select count(*) from ipv4s where dormitory_id=4) where id=4;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=5), computers_max = (select count(*) from ipv4s where dormitory_id=5) where id=5;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=6), computers_max = (select count(*) from ipv4s where dormitory_id=6) where id=6;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=7), computers_max = (select count(*) from ipv4s where dormitory_id=7) where id=7;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=8), computers_max = (select count(*) from ipv4s where dormitory_id=8) where id=8;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=9), computers_max = (select count(*) from ipv4s where dormitory_id=9) where id=9;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=10), computers_max = (select count(*) from ipv4s where dormitory_id=10) where id=10;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=11), computers_max = (select count(*) from ipv4s where dormitory_id=11) where id=11;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=12), computers_max = (select count(*) from ipv4s where dormitory_id=12) where id=12;
update dormitories set users_max=(select sum(users_max) from locations where dormitory_id=13), computers_max = (select count(*) from ipv4s where dormitory_id=13) where id=13;