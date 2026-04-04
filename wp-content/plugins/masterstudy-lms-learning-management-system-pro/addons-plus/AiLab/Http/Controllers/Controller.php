<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\AiContentGenerator;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Client;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Utility\Options;

class Controller {
	protected $client;
	protected $ai;
	protected $options;

	public function __construct() {
		$this->options = new Options();
		$this->client  = new Client( $this->options->get( 'api_key' ) );
		$this->ai      = new AiContentGenerator( $this->client );
	}
}
