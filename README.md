# Flarum Database Dumper

[![Latest Stable Version](https://img.shields.io/packagist/v/acpl/flarum-db-dumper)](https://packagist.org/packages/acpl/flarum-db-dumper) [![Total Downloads](https://img.shields.io/packagist/dt/acpl/flarum-db-dumper.svg)](https://packagist.org/packages/acpl/flarum-db-dumper/stats) [![GitHub Sponsors](https://img.shields.io/badge/Donate-%E2%9D%A4-%23db61a2.svg?&logo=github&logoColor=white&labelColor=181717)](https://github.com/android-com-pl/flarum-db-dumper?sponsor=1)

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

# Create backup on live site without locking tables
php flarum db:dump --single-transaction --quick --lock-tables=false
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

Additionally, most of the standard mysqldump options are supported (like `--single-transaction`, `--quick`, `--lock-tables`, etc).
Check mysqldump documentation for available options.

## Requirements

- `mysqldump` binary
- `gzip` for `.gz` compression
- `bzip2` for `.bz2` compression

## Links

- [Packagist](https://packagist.org/packages/acpl/flarum-db-dumper)
- [GitHub](https://github.com/android-com-pl/flarum-db-dumper)
- [Discuss](https://discuss.flarum.org/d/36911-database-dumper)
