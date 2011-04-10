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

$instructions = elgg_echo('spam_moderation:akismet_instruction');

$settings = <<<__HTML
<div>$instructions</div>
<div>$consumer_key_string $consumer_key_view</div>
__HTML;

echo $settings;
