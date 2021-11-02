<?php
namespace Tribe\Events\Event_Status;

use Tribe__Events__Main as Events_Plugin;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Template_Modifications
 *
 * @since   TBD
 *
 * @package Tribe\Events\Event_Status
 */
class Template_Modifications {
	/**
	 * Stores the template class used.
	 *
	 * @since TBD
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * Status Labels.
	 *
	 * @since TBD
	 *
	 * @var Status_Labels
	 */
	protected $status_labels;

	/**
	 * Template Modification constructor.
	 *
	 * @since TBD
	 *
	 * @param Template $template      An instance of the plugin template handler.
	 * @param Status_Labels $status_labels An instance of the statuses handler.
	 */
	public function __construct( Template $template, Status_Labels $status_labels ) {
		$this->template      = $template;
		$this->status_labels = $status_labels;
	}

	/**
	 * Gets the instance of template class set for the metabox.
	 *
	 * @since TBD
	 *
	 * @return Template Instance of the template we are using to render this metabox.
	 */
	public function get_template() {
		return $this->template;
	}

	/**
	 * Add the control classes for the views v2 elements
	 *
	 * @since TBD
	 *
	 * @param int|WP_Post      $event      Post ID or post object.
	 *
	 * @return string[]
	 */
	public function get_post_classes( $event ) {
		$classes = [];
		if ( ! tribe_is_event( $event ) ) {
			return $classes;
		}

		$event = tribe_get_event( $event );

		if ( $event->event_status ) {
			$classes[] = 'tribe-events-status__list-event-' . sanitize_html_class( $event->event_status );
		}

		return $classes;
	}

	/**
	 * Include the control markers to the single page.
	 *
	 * @since TBD
	 *
	 * @param  string  $notices_html  Previously set HTML.
	 * @param  array   $notices       Array of notices added previously.
	 *
	 * @return string  New Before with the control markers appended.
	 */
	public function add_single_status_reason( $notices_html, $notices ) {
		if ( ! is_singular( Events_Plugin::POSTTYPE ) ) {
			return $notices_html;
		}

		$args = [
			'event'         => tribe_get_event( get_the_ID() ),
			'status_labels' => $this->status_labels,
		];

		return $notices_html . $this->template->template( 'single/event-statuses', $args, false );
	}

	/**
	 * Inserts Status Label.
	 *
	 * @since TBD
	 *
	 * @param string   $hook_name        For which template include this entry point belongs.
	 * @param string   $entry_point_name Which entry point specifically we are triggering.
	 * @param Template $template         Current instance of the Template.
	 */
	public function insert_status_label( $hook_name, $entry_point_name, $template ) {
		$context = $template->get_values();
		$event   = Arr::get( $context, 'event', null );
		if ( ! $event instanceof WP_Post ) {
			return;
		}

		$args = [
			'event'         => $event,
			'status_labels' => $this->status_labels,
		];

		$this->template->template( 'event-status/status-label', $args );
	}
}
