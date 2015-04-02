SELECT lower(name) AS name, lower(surname) AS surname, count(*)
INTO holdkey
FROM users
GROUP BY lower(name), lower(surname)
HAVING count(*) > 1;

SELECT DISTINCT users.*
INTO holddups
FROM users, holdkey
WHERE lower(users.name) = lower(holdkey.name)
AND lower(users.surname) = lower(holdkey.surname)
AND active = false
AND modified_by is null
AND (SELECT count(*) FROM computers WHERE user_id = users.id) = 0;

DELETE FROM users WHERE id IN (SELECT id FROM holddups);

DROP TABLE holdkey;
DROP TABLE holddups;