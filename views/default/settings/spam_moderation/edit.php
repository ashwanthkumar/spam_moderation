<?php
/**
 *
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
