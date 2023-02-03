ALTER TABLE "episodes" ADD COLUMN "created" text DEFAULT '';
ALTER TABLE "feeds" ADD COLUMN "created" text DEFAULT '';
ALTER TABLE "os" ADD COLUMN "created" text DEFAULT '';
ALTER TABLE "useragents" ADD COLUMN "created" text DEFAULT '';
ALTER TABLE "devices" ADD COLUMN "created" text DEFAULT '';

ALTER TABLE "episodes" ADD COLUMN "uuid" text DEFAULT '';
ALTER TABLE "feeds" ADD COLUMN "uuid" text DEFAULT '';
ALTER TABLE "os" ADD COLUMN "uuid" text DEFAULT '';
ALTER TABLE "useragents" ADD COLUMN "uuid" text DEFAULT '';
ALTER TABLE "devices" ADD COLUMN "uuid" text DEFAULT '';

UPDATE "episodes" SET created = (Date(episodes.log_date));
UPDATE "feeds" SET created = (Date(feeds.log_date));
UPDATE "os" SET created = (os.log_date || "-01");
UPDATE "useragents" SET created = (useragents.log_date || "-01");
UPDATE "devices" SET created = (devices.log_date || "-01");

ALTER TABLE "episodes" DROP COLUMN "log_date";
ALTER TABLE "feeds" DROP COLUMN "log_date";
ALTER TABLE "os" DROP COLUMN "log_date";
ALTER TABLE "useragents" DROP COLUMN "log_date";
ALTER TABLE "devices" DROP COLUMN "log_date";

DROP TABLE settings;