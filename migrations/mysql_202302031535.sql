RENAME TABLE podcaster_devices TO devices,
    podcaster_episodes TO episodes,
    podcaster_feeds TO feeds,
    podcaster_os TO os,
    podcaster_useragents TO useragents;

ALTER TABLE episodes CHANGE COLUMN id id varchar(255);
ALTER TABLE devices CHANGE COLUMN podcaster_id id varchar(255);
ALTER TABLE feeds CHANGE COLUMN podcaster_id id varchar(255);
ALTER TABLE os CHANGE COLUMN podcaster_id id varchar(255);
ALTER TABLE useragents CHANGE COLUMN podcaster_id id varchar(255);

ALTER TABLE episodes ADD COLUMN uuid varchar(255) DEFAULT '';
ALTER TABLE feeds ADD COLUMN uuid varchar(255) DEFAULT '';
ALTER TABLE os ADD COLUMN uuid varchar(255) DEFAULT '';
ALTER TABLE useragents ADD COLUMN uuid varchar(255) DEFAULT '';
ALTER TABLE devices ADD COLUMN uuid varchar(255) DEFAULT '';

ALTER TABLE os ADD COLUMN created date NULL;
ALTER TABLE useragents ADD COLUMN created date NULL;
ALTER TABLE devices ADD COLUMN created date NULL;

ALTER TABLE episodes CHANGE COLUMN log_date created date;
ALTER TABLE feeds CHANGE COLUMN log_date created date;

ALTER TABLE os ADD COLUMN created date NULL;
ALTER TABLE useragents ADD COLUMN created date NULL;
ALTER TABLE devices ADD COLUMN created date NULL;

UPDATE os SET created = CONCAT(os.log_date, "-01");
UPDATE useragents SET created = CONCAT(useragents.log_date, "-01");
UPDATE devices SET created = CONCAT(devices.log_date, "-01");

ALTER TABLE os DROP COLUMN log_date;
ALTER TABLE useragents DROP COLUMN log_date;
ALTER TABLE devices DROP COLUMN log_date;
ALTER TABLE episodes DROP COLUMN podcaster_id;

DROP TABLE `podcaster_settings`