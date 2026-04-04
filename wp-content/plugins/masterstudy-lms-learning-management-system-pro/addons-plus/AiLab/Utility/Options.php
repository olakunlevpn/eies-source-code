<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Utility;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Model;

final class Options {
	private array $options = array();

	public function __construct() {
		$this->options = $this->get_options();

		$this->options['api_key']    = \STM_LMS_Options::get_option( 'openai_api_key' );
		$this->options['text_model'] = \STM_LMS_Options::get_option( 'openai_text_model', Model::GPT_35_TURBO );
		$this->options['results']    = array(
			'text'  => \STM_LMS_Options::get_option( 'openai_text_suggestions', 4 ),
			'image' => \STM_LMS_Options::get_option( 'openai_image_suggestions', 2 ),
		);
	}

	public function get( $name, $default = null ) {
		return ArrayHelper::get( $this->options, $name, $default );
	}

	private function get_options(): array {
		return array(
			'modules'   => array(
				'title'   => true,
				'text'    => true,
				'content' => true,
				'image'   => true,
			),
			'generator' => array(
				'title'   => array(
					'prompt' => "Create a short SEO-friendly title for this prompt: %s.\n No URLs, no apostrophes. ",
					'tokens' => 100,
				),
				'text'    => array(
					'prompt' => "Create a SEO-friendly text for this prompt: %s.\n Respond with HTML format for TinyMCE without comments. No URLs, no apostrophes, no titles. ",
					'tokens' => 500,
				),
				'content' => array(
					'prompt' => "Create a SEO-friendly content for this prompt: %s.\n Respond with HTML format for TinyMCE without comments. No URLs, no apostrophes. ",
					'tokens' => 1500,
					'tones'  => array(
						'Formal'         => 'Formal',
						'Casual'         => 'Casual',
						'Engaging'       => 'Engaging',
						'Professional'   => 'Professional',
						'Conversational' => 'Conversational',
					),
				),
				'quiz'    => array(
					'tokens' => 3000,
				),
				'image'   => array(
					'prompt' => '%s, in style %s',
					'tokens' => 320,
					'model'  => Model::DALL_E_3,
					'size'   => '1792x1024',
					'styles' => array(
						'Abstract'               => 'Abstract',
						'Abstract Expressionism' => 'Abstract Expressionism',
						'Action painting'        => 'Action painting',
						'Art Brut'               => 'Art Brut',
						'Art Deco'               => 'Art Deco',
						'Art Nouveau'            => 'Art Nouveau',
						'Baroque'                => 'Baroque',
						'Byzantine'              => 'Byzantine',
						'Caricature'             => 'Caricature',
						'Cartoon'                => 'Cartoon',
						'Classical'              => 'Classical',
						'Color Field'            => 'Color Field',
						'Conceptual'             => 'Conceptual',
						'Cubism'                 => 'Cubism',
						'Dada'                   => 'Dada',
						'Expressionism'          => 'Expressionism',
						'Fauvism'                => 'Fauvism',
						'Figurative'             => 'Figurative',
						'Futurism'               => 'Futurism',
						'Gothic'                 => 'Gothic',
						'Hard-edge painting'     => 'Hard-edge painting',
						'Hyperrealism'           => 'Hyperrealism',
						'Illustration'           => 'Illustration',
						'Impressionism'          => 'Impressionism',
						'Japonisme'              => 'Japonisme',
						'Luminism'               => 'Luminism',
						'Lyrical Abstraction'    => 'Lyrical Abstraction',
						'Mannerism'              => 'Mannerism',
						'Minimalism'             => 'Minimalism',
						'Naive Art'              => 'Naive Art',
						'New Realism'            => 'New Realism',
						'Neo-expressionism'      => 'Neo-expressionism',
						'Neo-pop'                => 'Neo-pop',
						'Op Art'                 => 'Op Art',
						'Opus Anglicanum'        => 'Opus Anglicanum',
						'Outsider Art'           => 'Outsider Art',
						'Pop Art'                => 'Pop Art',
						'Photorealism'           => 'Photorealism',
						'Pointillism'            => 'Pointillism',
						'Post-Impressionism'     => 'Post-Impressionism',
						'Realistic'              => 'Realistic',
						'Renaissance'            => 'Renaissance',
						'Rococo'                 => 'Rococo',
						'Romanticism'            => 'Romanticism',
						'Street Art'             => 'Street Art',
						'Studio Ghibli'          => 'Studio Ghibli',
						'Superflat'              => 'Superflat',
						'Surrealism'             => 'Surrealism',
						'Symbolism'              => 'Symbolism',
						'Tenebrism'              => 'Tenebrism',
						'Ukiyo-e'                => 'Ukiyo-e',
						'Western Art'            => 'Western Art',
						'YBA'                    => 'YBA',
						'None'                   => 'None',
					),
				),
			),
		);
	}
}
