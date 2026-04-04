<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi;

/**
 * Enum for OpenAI models.
 */
final class Model {
	public const GPT_5                   = 'gpt-5';
	public const GPT_5_MINI              = 'gpt-5-mini';
	public const GPT_5_NANO              = 'gpt-5-nano';
	public const GPT_4O                  = 'gpt-4o';
	public const GPT_4O_MINI             = 'gpt-4o-mini';
	public const GPT_41                  = 'gpt-4.1';
	public const GPT_41_MINI             = 'gpt-4.1-mini';
	public const GPT_41_NANO             = 'gpt-4.1-nano';
	public const GPT_4_TURBO             = 'gpt-4-turbo';
	public const GPT_4_TURBO_LATEST      = 'gpt-4-turbo-latest';
	public const GPT_4_TURBO_LATEST_MINI = 'gpt-4-turbo-latest-mini';
	public const GPT_4                   = 'gpt-4';
	public const GPT_35_TURBO            = 'gpt-3.5-turbo';
	public const GPT_35_TURBO_16K        = 'gpt-3.5-turbo-16k';
	public const GPT_35_TURBO_INSTRUCT   = 'gpt-3.5-turbo-instruct';

	// Images
	public const DALL_E_2 = 'dall-e-2';
	public const DALL_E_3 = 'dall-e-3';

	// Audio
	public const WHISPER_1             = 'whisper-1';
	public const GPT4O_TRANSCRIBE      = 'gpt-4o-transcribe';
	public const GPT4O_MINI_TRANSCRIBE = 'gpt-4o-mini-transcribe';
	public const TTS_1                 = 'tts-1';
	public const TTS_1_HD              = 'tts-1-hd';

	// Moderation
	public const MODERATION_LATEST = 'text-moderation-latest';
}
