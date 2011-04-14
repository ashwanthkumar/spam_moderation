<?php
/**
 *   Copyright SpamModeration Plugin Ashwanth Kumar <ashwanthkumar@googlemail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

 /**
 * 	This service plugin allows the system to validate the content posted on the community against Akismet spam service.
 *
 *	@package SpamModeration
 *	@date 14/04/2011 - Last Updated
 */

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
	
	'spam_moderation:multiple_services' => '* Using multiple services, will have performance issues on slow servers. ',
);

add_translation('en', $english);
