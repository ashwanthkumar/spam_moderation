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

$enable_akismet_label = elgg_echo('spam_moderation:enable_akismet');
$enable_akismet = elgg_view('input/dropdown', array(
	'name' => 'params[enable_akismet]',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no'),
	),
	'value' => $vars['entity']->enable_akismet ? $vars['entity']->enable_akismet : 'no',
));

$enable_keyword_filter_label = elgg_echo('spam_moderation:enable_keyword_filter');
$enable_keyword_filter = elgg_view('input/dropdown', array(
	'name' => 'params[enable_keyword_filter]',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no'),
	),
	'value' => $vars['entity']->enable_keyword_filter ? $vars['entity']->enable_keyword_filter : 'yes',
));

$keyword_label = elgg_echo('spam_moderation:keyword_label');
$keyword_list = elgg_view('input/plaintext', array(
	'name' => 'params[keyword_list]',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no'),
	),
	'value' => $vars['entity']->keyword_list ? $vars['entity']->keyword_list : '',
));


$instructions = elgg_echo('spam_moderation:akismet_instruction');

$settings = <<<__HTML
<div>$instructions</div>
<hr />
<div>$enable_akismet_label $enable_akismet</div>
<div>$consumer_key_string $consumer_key_view</div>
<hr />
<div>$enable_keyword_filter_label $enable_keyword_filter</div>
<div>$keyword_label $keyword_list</div>

__HTML;

echo $settings;
