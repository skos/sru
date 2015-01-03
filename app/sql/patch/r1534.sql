ALTER TABLE computers_aliases ADD COLUMN domain_name character varying(70) DEFAULT '.ds.pg.gda.pl';
UPDATE computers_aliases a SET domain_name = a.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i, computers c WHERE v.id = i.vlan AND i.ip = c.ipv4 AND c.id = a.computer_id);
ALTER TABLE computers_aliases ALTER domain_name SET NOT NULL;
ALTER TABLE computers_aliases ALTER domain_name DROP DEFAULT;

CREATE UNIQUE INDEX computers_aliases_domain_name_key
ON computers_aliases (domain_name);

ALTER TABLE computers_aliases DROP CONSTRAINT computers_aliases_host_key;