CREATE TABLE settings (
    podcaster_version VARCHAR NOT NULL UNIQUE
);

CREATE TABLE episodes (
    id VARCHAR UNIQUE,
    podcast_slug VARCHAR NOT NULL,
    podcast_name VARCHAR NOT NULL,
    episode_slug VARCHAR NOT NULL,
    episode_name VARCHAR NOT NULL,
    log_date TEXT NOT NULL,
    downloads INTEGER NOT NULL
);

CREATE TABLE feeds (
    id VARCHAR UNIQUE,
    podcast_slug VARCHAR NOT NULL,
    podcast_name VARCHAR NOT NULL,
    log_date TEXT NOT NULL,
    downloads INTEGER NOT NULL
);

CREATE TABLE os (
    id VARCHAR UNIQUE,
    os VARCHAR NOT NULL,
    podcast_slug VARCHAR NOT NULL,
    log_date TEXT NOT NULL,
    downloads INTEGER NOT NULL
);

CREATE TABLE useragents (
    id VARCHAR UNIQUE,
    useragent VARCHAR NOT NULL,
    podcast_slug VARCHAR NOT NULL,
    log_date TEXT NOT NULL,
    downloads INTEGER NOT NULL
);

CREATE TABLE devices (
    id VARCHAR UNIQUE,
    device VARCHAR NOT NULL,
    podcast_slug VARCHAR NOT NULL,
    log_date TEXT NOT NULL,
    downloads INTEGER NOT NULL
);
