<?php
// phpcs:ignoreFile

/**
 * @copyright  https://github.com/UsabilityDynamics/zoom-api-php-client/blob/master/LICENSE
 */

namespace Zoom\Api;

use Zoom\Contracts\Request;

/**
 * Class Meetings
 * @package Zoom\Api
 */
class Meetings extends Request {

	public function __construct() {
		parent::__construct();
	}


	public function create( string $userId, array $data = null ) {
		return $this->post( "users/{$userId}/meetings", $data );
	}


	public function meeting( string $meetingId ) {
		return $this->get( "meetings/{$meetingId}" );
	}

	public function remove( string $meetingId ) {
		return $this->delete( "meetings/{$meetingId}" );
	}

	public function update( string $meetingId, array $data = array() ) {
		return $this->patch( "meetings/{$meetingId}", $data );
	}

	public function status( string $meetingId, array $data = array() ) {
		return $this->put( "meetings/{$meetingId}/status", $data );
	}

	public function listRegistrants( string $meetingId, array $query = array() ) {
		return $this->get( "meetings/{$meetingId}/registrants", $query );
	}

}
