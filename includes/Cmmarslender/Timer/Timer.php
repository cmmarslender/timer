<?php

namespace Cmmarslender\Timer;

class Timer {

	const STATE_RESET = 0;
	const STATE_RUNNING = 1;
	const STATE_STOPPED = 2;

	/**
	 * Tracks the state of the timer.
	 *
	 * @var int
	 */
	public $state;

	/**
	 * When we started the timer.
	 *
	 * Used in elapsed time calculations.
	 *
	 * @var int
	 */
	public $start_time;

	/**
	 * When we stopped the timer.
	 *
	 * Used in elapsed time calculations.
	 *
	 * @var int
	 */
	public $stop_time;

	/**
	 * Total number of items.
	 *
	 * Used for average time per item calculations.
	 *
	 * @var int
	 */
	public $total_items;

	/**
	 * Current item count.
	 *
	 * Incremented by tick() and used in average time per item calculations.
	 *
	 * @var int
	 */
	public $current_item;

	public function __construct() {
		$this->reset();
	}

	public function set_total_items( $items ) {
		$this->total_items = (int) $items;
	}

	public function tick() {
		$this->current_item++;
	}

	public function is_running() {
		return (bool) ( self::STATE_RUNNING === $this->state );
	}

	public function start() {
		$this->start_time = time();
		$this->state = self::STATE_RUNNING;
	}

	public function stop() {
		$this->stop_time = time();
		$this->state = self::STATE_STOPPED;
	}

	public function reset() {
		$this->start_time = 0;
		$this->stop_time = 0;
		$this->total_items = 0;
		$this->current_item = 0;
		$this->state = self::STATE_RESET;
	}

	public function elapsed_time() {
		if ( empty( $this->start_time ) ) {
			// Timer hasn't started, so return 0
			return 0;
		}

		if ( empty( $this->stop_time ) ) {
			// Timer is still running, so use the current time
			$stop = time();
		} else {
			$stop = $this->stop_time;
		}

		return $stop - $this->start_time;
	}

	public function average() {
		if ( self::STATE_RESET === $this->state ) {
			return false;
		}

		$time = $this->elapsed_time();
		$items = $this->current_item;

		return ( $time / $items );
	}

	public function remaining_time() {
		$remaining_items = ( $this->total_items - $this->current_item );

		return (int) ( $remaining_items * $this->average() );
	}

	public function percent_complete( $precision = 2 ) {
		$percent = ( $this->current_item / $this->total_items ) * 100;

		return round( $percent, $precision );
	}

}
