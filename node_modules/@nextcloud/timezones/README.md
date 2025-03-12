# `@nextcloud/timezones`

[![npm last version](https://img.shields.io/npm/v/@nextcloud/timezones.svg?style=flat-square)](https://www.npmjs.com/package/@nextcloud/timezone-library)

A small library containing common time zone logic and data.

## API

The most important exported function is `getTimezoneManager()` which will return an instance of the `TimezoneManager` class.
This library ships the [tz database](https://www.iana.org/time-zones).
All bundled time zones and aliases can be loaded via `TimezoneManger.registerDefaultTimezones()`.
