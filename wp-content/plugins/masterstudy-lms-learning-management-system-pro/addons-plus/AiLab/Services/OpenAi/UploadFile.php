<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi;

final class UploadFile {
	public string $filename;
	public string $content;

	public function __construct( string $filename, string $content ) {
		$this->filename = $filename;
		$this->content  = $content;
	}
}
