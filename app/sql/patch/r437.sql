CREATE SEQUENCE switches_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE switches (
    id bigint DEFAULT nextval('switches_id_seq'::regclass) NOT NULL,
    model bigint NOT NULL,
    serial_no character varying(32) NOT NULL,
    localization character varying(128),
    comment pg_catalog.text,
    dormitory bigint NOT NULL,
    inventory_no character varying(32),
    received date,
    operational boolean NOT NULL,
    hierarchy_no integer,
    ipv4 inet
);

COMMENT ON COLUMN switches.localization IS 'umiejscowanie switcha';
COMMENT ON COLUMN switches.inventory_no IS 'numer inwentarzowy';
COMMENT ON COLUMN switches.received IS 'data dodania na stan';
COMMENT ON COLUMN switches.operational IS 'czy sprawny';
COMMENT ON COLUMN switches.hierarchy_no IS 'nr w hierarchi DSu';

CREATE SEQUENCE switches_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE switches_model (
    id bigint DEFAULT nextval('switches_type_id_seq'::regclass) NOT NULL,
    model_name character varying(32) NOT NULL,
    model_no character varying(8) NOT NULL,
    ports_no integer NOT NULL
);

COMMENT ON COLUMN switches_model.model_name IS 'opisowa nazwa modelu';
COMMENT ON COLUMN switches_model.model_no IS 'kod modelu wg producenta';
COMMENT ON COLUMN switches_model.ports_no IS 'liczba portow';

CREATE SEQUENCE switches_port_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE switches_port (
    id bigint DEFAULT nextval('switches_port_id_seq'::regclass) NOT NULL,
    switch bigint NOT NULL,
    location bigint,
    ordinal_no integer NOT NULL,
    comment character varying(255),
    connected_switch bigint,
    is_admin boolean DEFAULT false NOT NULL
);

COMMENT ON COLUMN switches_port.location IS 'lokalizacja podlaczona do portu';
COMMENT ON COLUMN switches_port.ordinal_no IS 'nr portu na switchu';
COMMENT ON COLUMN switches_port.connected_switch IS 'podlaczony do portu switch';
COMMENT ON COLUMN switches_port.is_admin IS 'czy port admina';

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_inventory_no_unique UNIQUE (inventory_no);

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_ipv4_unique UNIQUE (ipv4);

ALTER TABLE ONLY switches_model
    ADD CONSTRAINT switches_model_model_no_unique UNIQUE (model_no);

ALTER TABLE ONLY switches_model
    ADD CONSTRAINT switches_model_name_unique UNIQUE (model_name);

ALTER TABLE ONLY switches_model
    ADD CONSTRAINT switches_model_pkey PRIMARY KEY (id);

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_pkey PRIMARY KEY (id);

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_pkey PRIMARY KEY (id);

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_serial_no_unique UNIQUE (serial_no);


CREATE INDEX fki_switches_model_fkey ON switches USING btree (model);

CREATE INDEX fki_switches_port_connected_switch_fkey ON switches_port USING btree (connected_switch);

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_dormitories_fkey FOREIGN KEY (dormitory) REFERENCES dormitories(id);

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_model_fkey FOREIGN KEY (model) REFERENCES switches_model(id);

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_connected_switch_fkey FOREIGN KEY (connected_switch) REFERENCES switches(id);

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_location_fkey FOREIGN KEY (location) REFERENCES locations(id);

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_switch_fkey FOREIGN KEY (switch) REFERENCES switches(id);

INSERT INTO switches_model (id, model_name, model_no, ports_no) VALUES (1, 'HP2848', 'J4904A', 48);
INSERT INTO switches_model (id, model_name, model_no, ports_no) VALUES (2, 'HP2810-48G', 'J9022A', 48);
INSERT INTO switches_model (id, model_name, model_no, ports_no) VALUES (3, 'HP3400cl-48G', 'J4906A', 48);
INSERT INTO switches_model (id, model_name, model_no, ports_no) VALUES (4, 'HP3500yl-48G', 'J8693A', 48);
INSERT INTO switches_model (id, model_name, model_no, ports_no) VALUES (5, 'HP6108', 'J4902A', 8);
INSERT INTO switches_model (id, model_name, model_no, ports_no) VALUES (6, 'HP2810-24G', 'J9021A', 24);