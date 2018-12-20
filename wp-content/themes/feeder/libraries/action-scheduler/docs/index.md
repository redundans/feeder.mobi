## Overview

Action Scheduler is a library for triggering a hook to run at some time in the future. Each hook can be scheduled with unique data, to allow callbacks to perform operations on that data. The hook can also be scheduled to run on one or more occassions.

Think of it like an extension to `do_action()` which adds the ability to delay and repeat a hook.

### Battle-Tested

Every month, Action Scheduler processes millions of payments for [Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/), webhooks for [WooCommerce](https://wordpress.org/plugins/woocommerce/), as well as emails and other events for a range of other plugins.

It's been seen on live sites processing queues in excess of 50,000 jobs and doing resource intensive operations, like processing payments and creating orders, at a sustained rate of over 10,000 / hour without negatively impacting normal site operations.

This is all on infrastructure and WordPress sites outside the control of the plugin author.

If your plugin needs background processing, especially of large sets of tasks, Action Scheduler can help.

### How it Works

Action Scheduler uses a WordPress [custom post type](http://codex.wordpress.org/Post_Types), creatively named `scheduled-action`, to store the hook name, arguments and scheduled date for an action that should be triggered at some time in the future.

The scheduler will attempt to run every minute by attaching itself as a callback to the `'action_scheduler_run_schedule'` hook, which is scheduled using WordPress's built-in [WP-Cron](http://codex.wordpress.org/Function_Reference/wp_cron) system.

When triggered, Action Scheduler will check for posts of the `scheduled-action` type that have a `post_date` at or before this point in time i.e. actions scheduled to run now or at sometime in the past.

### Batch Processing

If there are actions to be processed, Action Scheduler will stake a unique claim for a batch of 20 actions and begin processing that batch. The PHP process spawned to run the batch will then continue processing batches of 20 actions until it times out or exhausts available memory.

If your site has a large number of actions scheduled to run at the same time, Action Scheduler will process more than one batch at a time. Specifically, when the `'action_scheduler_run_schedule'` hook is triggered approximately one minute after the first batch began processing, a new PHP process will stake a new claim to a batch of actions which were not claimed by the previous process. It will then begin to process that batch.

This will continue until all actions are processed using a maximum of 5 concurrent queues.

### Housekeeping

Before processing a batch, the scheduler will remove any existing claims on actions which have been sitting in a queue for more than five minutes.

Action Scheduler will also trash any actions which were completed more than a month ago.

If an action runs for more than 5 minutes, Action Scheduler will assume the action has timed out and will mark it as failed. However, if all callbacks attached to the action were to successfully complete sometime after that 5 minute timeout, its status would later be updated to completed.

### Record Keeping

Events for each action will be also logged in the [comments table](http://codex.wordpress.org/Database_Description#Table_Overview).

The events logged by default include when an action:
 * is created
 * starts
 * completes
 * fails

Actions can also be grouped together using a custom taxonomy named `action-group`.

## Credits

Developed and maintained by [Prospress](http://prospress.com/) in collaboration with [Flightless](https://flightless.us/).

Collaboration is cool. We'd love to work with you to improve Action Scheduler. [Pull Requests](https://github.com/prospress/action-scheduler/pulls) welcome.