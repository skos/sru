ALTER TABLE dormitories ADD COLUMN name_en character varying(255);
ALTER TABLE faculties ADD COLUMN name_en character varying(255);

UPDATE dormitories set name_en = 'Dormitory No. 1' where id = 1;
UPDATE dormitories set name_en = 'Dormitory No. 2' where id = 2;
UPDATE dormitories set name_en = 'Dormitory No. 3' where id = 3;
UPDATE dormitories set name_en = 'Dormitory No. 4' where id = 4;
UPDATE dormitories set name_en = 'Dormitory No. 5' where id = 5;
UPDATE dormitories set name_en = 'Dormitory No. 6' where id = 7;
UPDATE dormitories set name_en = 'Dormitory No. 7' where id = 8;
UPDATE dormitories set name_en = 'Dormitory No. 8' where id = 9;
UPDATE dormitories set name_en = 'Dormitory No. 9' where id = 10;
UPDATE dormitories set name_en = 'Dormitory No. 10' where id = 11;
UPDATE dormitories set name_en = 'Dormitory No. 11' where id = 12;
UPDATE dormitories set name_en = 'Staff Hotel in Jelitkowo' where id = 13;

UPDATE faculties set name_en = 'Electronics, Telecommunications and Informatics' where id = 1;
UPDATE faculties set name_en = 'Mechanical Engineering' where id = 2;
UPDATE faculties set name_en = 'Chemical Faculty' where id = 3;
UPDATE faculties set name_en = 'Management and Economics' where id = 4;
UPDATE faculties set name_en = 'Architecture' where id = 5;
UPDATE faculties set name_en = 'Civil and Environmental Engineering' where id = 6;
UPDATE faculties set name_en = 'Electrical and Control Engineering' where id = 7;
UPDATE faculties set name_en = 'Applied Physics and Mathematics' where id = 8;
UPDATE faculties set name_en = 'Ocean Engineering and Ship Technology' where id = 9;