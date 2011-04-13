<?php
/**
 * An english language definition file
 */

$english = array(
	'spam_moderation' => 'SPAM Detection service for Elgg',

	'spam_moderation:akismet_key' => 'API Key for Akismet SPAM Detection Service',
	'spam_moderation:enable_akismet' => 'Use Akismet Service*',
	'spam_moderation:enable_keyword_filter' => 'Use Internal keyword based filter*',
	'spam_moderation:keyword_label' => 'Keyword(s) List (Comma separated)',
	
	'spam_moderation:akismet_instruction' => 'You must obtain a API key from <a href="https://akismet.com/signup/#free" target="_blank">Akismet</a>, this should take only a few minutes.',
	
	'spam_moderation:multiple_services' => '* Using both type of services, will have performance issues on slow servers. Also, if the post is large there might be some issue regarding the same. ',
);

add_translation('en', $english);
