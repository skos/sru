ALTER TABLE switches RENAME COLUMN operational TO inoperational;

update switches set inoperational=NOT inoperational;