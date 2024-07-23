# Boo (👻 Backup, Optimize & Oversee)

BOO is a stand-alone PHP Laravel project that provide [Spatie's Laravel Backup](https://github.com/spatie/laravel-backup) commands and other tools.

It allows you to easily save the contents of a project's production folder and its database in a s3 bucket using a scheduled job.
It is also possible to duplicate one or more S3 buckets during this backup.

## Requirements

* PHP 8.1 >
* Composer 2
* MySQL | PostgreSQL

## Setup

Create and edit your own `.env` file with [.env.example](.env.example)

```bash
cp .env.example .env
```

Generate application key and clear cache

```bash
make install
```

## Scheduler

Create a new cronjob called every minute like this:

```ini
* * * * * php /your/project/directory/location/artisan schedule:run
```

## Docker setup

Use this Docker image to run backups: https://hub.docker.com/r/webidfr/boo

Requirements:

- Mount of your `.env` file (use [.env.example](.env.example) as template) as `/application/.env` inside the container.
- Mount the target directory to backup as `/target` inside the container.
- Mount your `rclone.conf` as `/root/.config/rclone/rclone.conf` inside the container for extra S3 buckets backup.
- Use the `host` network to allow access to your local MySQL server.

Create and edit your own `.env` file with [.env.example](.env.example)

ℹ️ mysqldump Ver 10.19 Distrib 10.11.6-MariaDB is installed on every image. Only various postgres versions images are available.

```bash
curl https://raw.githubusercontent.com/web-id-fr/boo/main/.env.example --output .env
```

Set the target directory as the volume path inside the container

```ini
BACKUP_PROJECT_DIRECTORY=/target
```

Example command to list backups done.

```bash
docker run --rm \
    -v $(pwd)/.env:/application/.env \
    -v /your/path/to/backup:/target \
    --network="host" \
    webidfr/boo:postgres-16 php artisan backup:list
```

### Scheduler

Create a new cronjob called every minute like this:

```
* * * * * docker run --rm -v /your/path/.env:/application/.env -v /your/path/to/backup:/target --network="host" webidfr/boo:postgres-16 schedule:run
```

## Tests

```bash
make tests
```

