DROP VIEW v_inventory_list;
CREATE VIEW v_inventory_list AS
SELECT ic.id as card_id, c.id, ic.dormitory_id as ic_dormitory_id, l.dormitory_id, c.location_id, ic.serial_no, ic.inventory_no, ic.received, c.device_model_id, m.name as device_model_name, 1 as table_id FROM computers c LEFT JOIN inventory_cards ic ON c.inventory_card_id=ic.id JOIN locations l ON c.location_id = l.id JOIN device_models m ON device_model_id = m.id WHERE c.type_id = 41 OR c.type_id = 43
UNION
SELECT ic.id as card_id, s.id, ic.dormitory_id as ic_dormitory_id, l.dormitory_id, s.location_id, ic.serial_no, ic.inventory_no, ic.received, 0, sm.model_name as device_model_name, 2 as table_id FROM switches s LEFT JOIN inventory_cards ic ON s.inventory_card_id=ic.id JOIN locations l ON s.location_id = l.id JOIN switches_model sm ON s.model = sm.id
UNION
SELECT ic.id as card_id, d.id, ic.dormitory_id as ic_dormitory_id, l.dormitory_id, d.location_id, ic.serial_no, ic.inventory_no, ic.received, d.device_model_id, m.name as device_model_name, 3 as table_id FROM devices d LEFT JOIN inventory_cards ic ON d.inventory_card_id=ic.id JOIN locations l ON d.location_id = l.id JOIN device_models m ON device_model_id = m.id;