# Flarum DB Dumper

Database backup extension for Flarum that allows dumping database content using the `db:dump` command.

## Installation

```sh
composer require acpl/flarum-db-dumper
```

## Usage

Basic usage:
```sh
# Dump to storage/dumps/dump-YYYY-MM-DD-HHMMSS.sql
php flarum db:dump

# Dump to specific path/file
php flarum db:dump /path/to/backup.sql
php flarum db:dump ../backups/forum.sql

# Dump with compression (based on extension)
php flarum db:dump /backups/dump.sql.gz   # gzip compression
php flarum db:dump /backups/dump.sql.bz2  # bzip2 compression
```

### Options

- `--compress`: Use compression (`gz` or `bz2`), e.g. `--compress=gz` for gzip
- `--include-tables=table1,table2`: Include only specific tables
- `--exclude-tables=table1,table2`: Exclude specific tables
- `--skip-structure`: Skip table structure
- `--no-data`: Skip table data, dump only structure
- `--skip-auto-increment`: Skip AUTO_INCREMENT values
- `--no-column-statistics`: Disable column statistics (for MySQL 8 compatibility)
- `--binary-path=/path/to/binary`: Custom mysqldump binary location

## Requirements

- `mysqldump` binary
- `gzip` for `.gz` compression
- `bzip2` for `.bz2` compression

## Links

- [Packagist](https://packagist.org/packages/acpl/flarum-db-dumper)
- [GitHub](https://github.com/android-com-pl/flarum-db-dumper)
