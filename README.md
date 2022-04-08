# feeder.mobi
feeder.mobi is a web service for subscribing to ATOM & RSS feeds and have them send as mobi-books to a Amazone kindle device. The project is built as a theme for WordPress and utilizes some open source libraries like SimplePie and PHPePub.

## Requirements
* Composer
* KindleGen

## Installation
1. Clone the repo inte your web root folder.
2. Run `composer install` in the project root folder to install Bedrock.
5. Copy .env.example to .env and update environment variables in `.env` file.
6. Add KINDLEGEN to .env file to point php to the kindlegen binary file iex: `KINDLEGEN='/usr/local/bin/kindlegen'`
2. Run `composer install` in the theme folder `wp-content/themes/feeder/`.
3. Activate `action-scheduler` and `buddypress` in plugins.
4. Activate the theme and set up a setting and feeds page with the right page templates.

## Problems?
For bug reports and feature requests, visit the issue tracker.