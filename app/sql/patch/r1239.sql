ALTER TABLE vlans ADD COLUMN task_export boolean NOT NULL DEFAULT false;
COMMENT ON COLUMN vlans.task_export IS 'czy wysylac dane do TASK';
