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
 *	@date 17/04/2011 - Last Updated
 */

/**
 *	Checks for blog post content SPAM
 *
 *	@param $entity ElggObject
 *	@param $service SpamService Plugin Setting
 **/
function check_for_spam_in_blog($entity, $service = "keyword") {
	
	$site = elgg_get_site_url();
	$user = get_entity($entity->owner_guid);
	
	switch($service) {
		case "akismet":
			$api_key = elgg_get_plugin_setting('akismet_key', 'spam_moderation');
			
			// Check for a valid key for the plugin
			if(!isset($api_key) || empty($api_key)) {
				register_error("API Key for Akismet is not set! Please contact the site administrator, for more details. ");
				// forward(REFERER);
				return false;
			}

			
			$akismet = new Akismet("$site/blog/", $api_key);
			
			// Setting the content properties for better SPAM detection
			$akismet->setCommentAuthor($user->name);
			$akismet->setCommentAuthorEmail($user->email);
			$akismet->setCommentContent($entity->description);
			$akismet->setPermalink($entity->getURL());

			if($akismet->isCommentSpam()) {
				// @TODO: Provide a method to override in case of a mis-diagnosis
				register_error("Content is marked as spam and is saved as draft. You cannot publish a post which is detected as SPAM. Contact site administrator for more details. ");

				$entity->is_spam = TRUE;
				$entity->spam_source = $service;
				$entity->status = "draft"; // Save the post only as draft
				// Change the entity access to private, else its still visible if the user knows the URL (Post Id can be easily guessed)
				$entity->access_id = 0;

				// Do not save the post yet!
				return false; 
			} else {
			  // store the post normally
			  $entity->is_spam = FALSE;
			}
		break;
		
		case "mollom":
			$mollom_public_key = elgg_get_plugin_setting('mollom_public_key','spam_moderation');
			$mollom_private_key = elgg_get_plugin_setting('mollom_private_key','spam_moderation');
			
			Mollom::setPublicKey($mollom_public_key);
			Mollom::setPrivateKey($mollom_private_key);
			Mollom::setServerList(array('http://xmlrpc3.mollom.com', 'http://xmlrpc2.mollom.com', 'http://xmlrpc1.mollom.com'));

			try {
				// get feedback
				$feedback = Mollom::checkContent(null, $entity->title, $entity->description, $user->name, null, $user->email, null, $user->guid);
			} catch (Exception $e) {
				// @todo handle the error here
				register_error($e->getMessage());
				return false;
			}
			
			if(in_array($feedback['spam'], array('unsure', 'unknown')))  {
				// @todo System not sure. What do we do? 
				
				// Validating based on the quality score.
				// If QualityScore < 0.5 and its unsure, then its most likely SPAM.
				if($feedback['quality'] < 0.5) {
					// Content is a pure SPAM! 
					register_error("Content is marked as spam and is saved as draft. You cannot publish a post which is detected as SPAM. Contact site administrator for more details. ");

					$entity->is_spam = TRUE;
					$entity->spam_source = $service;
					$entity->status = "draft"; // Save the post only as draft
					// Change the entity access to private, else its still visible if the user knows the URL (Post Id can be easily guessed)
					$entity->access_id = 0;

					return false;
				} else {
					// Benefit of doubt goes to the author and content
					return true;
				}
			} else if($feedback['spam'] == 'ham') { // Content is OK!
				return true;
			} else if($feedback['spam'] == 'spam') {
				// Content is a pure SPAM! 
				register_error("Content is marked as spam and is saved as draft. You cannot publish a post which is detected as SPAM. Contact site administrator for more details. ");

				$entity->is_spam = TRUE;
				$entity->spam_source = $service;
				$entity->status = "draft"; // Save the post only as draft
				// Change the entity access to private, else its still visible if the user knows the URL (Post Id can be easily guessed)
				$entity->access_id = 0;
				
				return false;
			}
		break;
		
		case "keyword":
		default:
			$keywords = elgg_get_plugin_setting('keyword_list','spam_moderation');
			
			// Extract the keywords out of the list
			$list = explode(',',$keywords);
			
			// Building the regular expression 
			$regxp = "/";
			foreach($list as $word) {
				$regxp = "$regxp$word|";
			}
			$regxp .= "/";
			
			// Doing the check here
			$keywords_cnt = preg_match_all($regxp,$entity->description,$filter_chk);
			
			if($keywords_cnt > 0) {
				// Keyword instances found!
				register_error("Content is marked as spam and is saved as draft. You cannot publish a post which is detected as SPAM. Contact site administrator for more details. ");
				
				$entity->is_spam = TRUE;
				$entity->spam_source = $service;
				$entity->status = "draft"; // Save the post only as a draft
				// Change the entity access to private, else its still visible if the user knows the URL (Post Id can be easily guessed)
				$entity->access_id = 0;

				return false;
			} else {
				$entity->is_spam = FALSE;
				return true;
			}
		break;
	}
}

/**
 *	Check for SPAM in Group topic reply
 *
 *	@param $entity ElggAnnotationObject
 *	@param $service SpamService Type to be used 
 **/
function check_for_group_topic_reply($entity, $service = "keyword") {
	
	$site = elgg_get_site_url();
	$user = get_entity($entity->owner_guid);

	$group_topic_entity = get_entity($entity->entity_guid);
	$content = $entity->value;

	switch($service) {
		case "akismet":
			$api_key = elgg_get_plugin_setting('akismet_key', 'spam_moderation');
			
			// Check for a valid key for the plugin
			if(!isset($api_key) || empty($api_key)) {
				register_error("API Key for Akismet is not set! Please contact the site administrator, for more details. ");
				// forward(REFERER);
				return false;
			}

			
			$akismet = new Akismet("$site/pg/group/", $api_key);
			
			// Setting the content properties for better SPAM detection
			$akismet->setCommentAuthor($user->name);
			$akismet->setCommentAuthorEmail($user->email);
			$akismet->setCommentContent($content);
			$akismet->setPermalink($group_topic_entity->getURL());

			if($akismet->isCommentSpam()) {
				// @TODO: Provide a method to override in case of a mis-diagnosis
				register_error("Sorry your reply could not be posted, as it was detected to be a potential spam content. ");

				// Do not save the content
				return false;
			} else {
				// Nothing more here, so just save it
			  return true;
			}
		break;
		
		case "mallom":
		// @todo
			$mollom_public_key = elgg_get_plugin_setting('mollom_public_key','spam_moderation');
			$mollom_private_key = elgg_get_plugin_setting('mollom_private_key','spam_moderation');
			
			Mollom::setPublicKey($mollom_public_key);
			Mollom::setPrivateKey($mollom_private_key);
			Mollom::setServerList(array('http://xmlrpc3.mollom.com', 'http://xmlrpc2.mollom.com', 'http://xmlrpc1.mollom.com'));

			try {
				// get feedback
				$feedback = Mollom::checkContent(null, $group_topic_entity->title, $content, $user->name, null, $user->email, null, $user->guid);
			} catch (Exception $e) {
				// @todo handle the error here
				register_error($e->getMessage());
				return false;
			}
			
			if(in_array($feedback['spam'], array('unsure', 'unknown')))  {
				// @todo System not sure. What do we do? 
				
				// Validating based on the quality score.
				// If QualityScore < 0.5 and its unsure, then its most likely SPAM.
				if($feedback['quality'] < 0.5) {
					// Content is a pure SPAM! 
				register_error("Sorry your reply could not be posted, as it was detected to be a potential spam content. ");
				return false;
				} else {
					// Benefit of doubt goes to the author and content
					return true;
				}
			} else if($feedback['spam'] == 'ham') { // Content is OK!
				return true;
			} else if($feedback['spam'] == 'spam') {
				// Content is a pure SPAM! 
				register_error("Sorry your reply could not be posted, as it was detected to be a potential spam content. ");
				return false;
			}
		break;
		
		case "keyword":
			$keywords = elgg_get_plugin_setting('keyword_list','spam_moderation');
			
			// Extract the keywords out of the list
			$list = explode(',',$keywords);
			
			// Building the regular expression 
			$regxp = "/";
			foreach($list as $word) {
				$regxp = "$regxp$word|";
			}
			$regxp .= "/";
			
			// Doing the check here
			$keywords_cnt = preg_match_all($regxp,$content,$filter_chk);
			
			if($keywords_cnt > 0) {
				// Spam content match
				register_error("Sorry your reply could not be posted, as it was detected to be a potential spam content. ");
				return false;
			} 
		break;
	}
}

/**
 *	Generic SPAM Checking handler for EgllEntity type of objects
 *
 *	@todo Extend it for better
 **/
function check_for_spam_in_generic_entities($entity) {
	$keywords = elgg_get_plugin_setting('keyword_list','spam_moderation');
	
	// Extract the keywords out of the list
	$list = explode(',',$keywords);
	
	// Building the regular expression 
	$regxp = "/";
	foreach($list as $word) {
		$regxp = "$regxp$word|";
	}
	$regxp .= "/";
	
	// Doing the check here
	$keywords_cnt = preg_match_all($regxp,$entity->description,$filter_chk);
	
	if($keywords_cnt > 0) { // Match found
		register_error("Sorry the Object can't be saved. It is identified to be a potential spam. ");
		return false;
	}
}

