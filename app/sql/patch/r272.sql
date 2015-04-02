COMMENT ON COLUMN penalty_templates.description IS 'opis dla administratora';

ALTER TABLE penalty_templates ADD COLUMN reason pg_catalog.text NOT NULL DEFAULT ''::pg_catalog.text;

UPDATE penalty_templates SET reason=description;

UPDATE penalty_templates SET description='Stawianie NAT-ow na kompie, uzywanie routera z wlaczonym NAT-em, udostepnianie polaczenia sieciowego' WHERE id=1;
UPDATE penalty_templates SET description='Ustawiony w komputerze cudzy adres MAC' WHERE id=2;
UPDATE penalty_templates SET description='Zbyt wysoki upload' WHERE id=3;
UPDATE penalty_templates SET description='Zbyt wysoki upload w ciagu 30 dni po ostatniej karze' WHERE id=4;
UPDATE penalty_templates SET description='Glownie rozsylanie spamu' WHERE id=5;
UPDATE penalty_templates SET description='Ponowna skarga za spam w ciagu 30 dni od ostatniej' WHERE id=6;
UPDATE penalty_templates SET description='Skarga za piractwo' WHERE id=7;
UPDATE penalty_templates SET description='Ponowna skarga za piractwo (w ciagu X dni od ostatniej kary za to)' WHERE id=8;
UPDATE penalty_templates set amnesty_after = duration;

ALTER TABLE penalties
   ADD COLUMN template_id smallint;
COMMENT ON COLUMN penalties.template_id IS 'id szablonu, na podstawie ktorego zostala utworzona kara';

update penalties set amnesty_after = end_at where amnesty_after is null;

