<?php

namespace TEC\Events\Custom_Tables\V1\Events\Event_Cleaner;

use tad_DI52_ServiceProvider as Service_Provider;
use Tribe__Events__Event_Cleaner_Scheduler;
use Tribe__Events__Main;

/**
 * Class Provider
 *
 * This is the provider for our "Old" Event Cleaner system.
 *
 * @since   TBD
 *
 * @package TEC\Events\Custom_Tables\V1\Events\Event_Cleaner
 */
class Provider extends Service_Provider {
	/**
	 * A flag property indicating whether the Service Provide did register or not.
	 *
	 * @since TBD
	 *
	 * @var bool
	 */
	private $did_register = false;

	/**
	 * Registers the filters and implementations required by the Custom Tables implementation.
	 *
	 * @since TBD
	 *
	 * @return bool Whether the Provider did register or not.
	 */
	public function register() {

		if ( $this->did_register ) {
			// Let's avoid double filtering by making sure we're registering at most once.
			return true;
		}

		$this->did_register = true;

		$this->remove_old_recurrence_cleaners();
		add_filter( 'tribe_events_delete_old_events_sql', [ $this, 'redirect_old_events_sql' ], 9 );
	}

	/**
	 * Deprecating/removing 'tec.event-cleaner' and the scheduler. This is now being handled by the CT1 Event Cleaner.
	 * system in CT1.
	 *
	 * @since TBD
	 */
	public function remove_old_recurrence_cleaners() {
		// Hide from settings page.
		add_filter( 'tribe_settings_tab_fields', function ( $args, $id ) {
			if ( $id == 'general' ) {
				$event_cleaner = tribe( 'tec.event-cleaner' );
				unset( $args[ $event_cleaner->key_delete_events ] );
			}

			return $args;
		}, 99, 2 );

		// Remove scheduled cleaner task.
		add_action( 'init', function () {
			$main = Tribe__Events__Main::instance();
			if ( isset( $main->scheduler ) ) {
				remove_action( Tribe__Events__Event_Cleaner_Scheduler::$del_cron_hook, [
					$main->scheduler,
					'permanently_delete_old_events'
				], 10 );
				wp_unschedule_event( time(), Tribe__Events__Event_Cleaner_Scheduler::$del_cron_hook );
			}
		}, 999 );
	}


	/**
	 * Hooks into our automated event cleaner service, and modifies the expired events query to handle only single
	 * occurrences.
	 *
	 * @since TBD
	 *
	 * @param string $sql The original query to retrieve expired events.
	 *
	 * @return string The modified CT1 query to retrieve expired events.
	 */
	public function redirect_old_events_sql( string $sql ): string {
		return tribe( Event_Cleaner::class )->redirect_old_events_sql( $sql );
	}
}