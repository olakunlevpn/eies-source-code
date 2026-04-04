<?php
// phpcs:ignoreFile

/**
 * @copyright  https://github.com/UsabilityDynamics/zoom-api-php-client/blob/master/LICENSE
 */

namespace Zoom\Api;

use Zoom\Contracts\Request;

/**
 * Class Reports
 * @package Zoom\Contracts
 */
class Reports extends Request {

	/**
	 * Meetings constructor.
	 *
	 * @param $apiKey
	 * @param $apiSecret
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Meeting Participants
	 *
	 * @param $meetingUUID
	 * @param array $query
	 *
	 * @return array|mixed
	 */
	public function meetingParticipants( string $meetingUUID, array $query = array() ) {
		return $this->get( "report/meetings/{$meetingUUID}/participants", $query );
	}

	public function dailyReports( array $query = array() ) {
		return $this->get( 'report/daily/', $query );
	}

}
