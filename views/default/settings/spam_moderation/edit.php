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

$consumer_key_string = elgg_echo('spam_moderation:akismet_key');
$consumer_key_view = elgg_view('input/text', array(
	'name' => 'params[akismet_key]',
	'value' => $vars['entity']->akismet_key,
	'class' => 'text_input',
));

$mollom_private_key_label = elgg_echo('spam_moderation:mollom_private_key_label');
$mollom_private_key = elgg_view('input/text', array(
	'name' => 'params[mollom_private_key]',
	'value' => $vars['entity']->mollom_private_key,
	'class' => 'text_input',
));

$mollom_public_key_label = elgg_echo('spam_moderation:mollom_public_key_label');
$mollom_public_key = elgg_view('input/text', array(
	'name' => 'params[mollom_public_key]',
	'value' => $vars['entity']->mollom_public_key,
	'class' => 'text_input',
));

// Write an action for this? May be.. @todo
$akismet_consumer_key_validate = elgg_view('input/button', array(
	'name' => 'params[consumer_key_validate]',
	'value' => 'Validate Key',
));

$spam_service = elgg_view('input/dropdown', array(
	'name' => 'params[spam_service]',
	'options_values' => array(
		'akismet' => elgg_echo('Akismet'),
		'mollom' => elgg_echo('Mollom'),
		'keyword' => elgg_echo('Internal Keyword Filter'),
	),
	'value' => $vars['entity']->spam_service ? $vars['entity']->spam_service : 'keyword',
));

$keyword_label = elgg_echo('spam_moderation:keyword_label');
$keyword_list = elgg_view('input/plaintext', array(
	'name' => 'params[keyword_list]',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no'),
	),
	'value' => $vars['entity']->keyword_list ? $vars['entity']->keyword_list : '',
	'rows' => '10',
	'cols' => '55',
));


$instructions = elgg_echo('spam_moderation:akismet_instruction');
$multiple_services = elgg_echo('spam_moderation:multiple_services');
$example_keyword_list = elgg_echo('spam_moderation:example_keyword_list');

$settings = <<<__HTML
<div>$instructions</div>
<div>$spam_service</div>
<hr />
<div>$mollom_public_key_label $mollom_public_key</div>
<div>$mollom_private_key_label $mollom_private_key</div>
<hr />
<div>$consumer_key_string $consumer_key_view</div>
<!-- <div>$akismet_consumer_key_validate</div> -->
<hr />
<div>$keyword_label </div>
<div>$keyword_list</div>
<div>$example_keyword_list</div>
__HTML;

echo $settings;
