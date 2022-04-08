# feeder.mobi
feeder.mobi is a web service for subscribing to ATOM & RSS feeds and have them send as mobi-books to a Amazone kindle device. The project is built as a theme for WordPress and utilizes some open source librarys like SimplePie and PHPePub.

## Requirements
* WordPress (tested with 5.0.2)
* action-scheduler
* Composer
* KindleGen

## Installation
1. Clone the repo inte your WordPress root folder.
2. Run `composer install` in the theme folder `wp-content/themes/feeder/`.
3. Activate `action-scheduler` in plugins.
4. Activate the theme and set up a setting and feeds page with the right page templates.

## Problems?
For bug reports and feature requests, visit the issue tracker.