ALTER TABLE ipv4s
   ALTER COLUMN dormitory_id DROP NOT NULL;

insert into ipv4s (ip, dormitory_id) select ('153.19.223.'||x.i)::inet, null from generate_series(0,254) as x(i);
insert into ipv4s (ip, dormitory_id) select ('153.19.208.'||x.i)::inet, null from generate_series(1,255) as x(i);