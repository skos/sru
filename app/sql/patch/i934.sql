DROP TRIGGER computer_update_domain_name ON computers;
DROP FUNCTION computers_set_domain_name();
ALTER TABLE computers ALTER domain_name DROP DEFAULT;

CREATE OR REPLACE FUNCTION computers_add_domain_name()
  RETURNS trigger AS
$BODY$BEGIN
	NEW.domain_name = NEW.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i WHERE v.id = i.vlan AND i.ip = NEW.ipv4);
return NEW;
END;$BODY$
  LANGUAGE plpgsql;
ALTER FUNCTION computers_add_domain_name()
  OWNER TO sru;
COMMENT ON FUNCTION computers_add_domain_name() IS 'ustawienie nazwy domenowej';

CREATE OR REPLACE FUNCTION computers_set_domain_name()
  RETURNS trigger AS
$BODY$BEGIN
	IF
		NEW.host!=OLD.host OR
		OLD.ipv4!=NEW.ipv4
	THEN
		NEW.domain_name = NEW.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i WHERE v.id = i.vlan AND i.ip = NEW.ipv4);
	END IF;
return NEW;
END;$BODY$
  LANGUAGE plpgsql;
ALTER FUNCTION computers_set_domain_name()
  OWNER TO sru;
COMMENT ON FUNCTION computers_set_domain_name() IS 'aktualizacja nazwy domenowej';

CREATE TRIGGER computer_add_domain_name
  BEFORE INSERT
  ON computers
  FOR EACH ROW
  EXECUTE PROCEDURE computers_add_domain_name();

CREATE TRIGGER computer_set_domain_name
  BEFORE UPDATE
  ON computers
  FOR EACH ROW
  EXECUTE PROCEDURE computers_set_domain_name();