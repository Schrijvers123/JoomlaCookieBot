<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Cookiebot
 *
 * @copyright   Copyright (C) 2005 - 2018 SCHRIJVERS123.NL. All rights reserved.
 * @license     GNU GPL v3 or later
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Joomla! Cookiebot plugin.
 *
 * @since  1.5
 */
class PlgSystemCookiebot extends JPlugin
{
	public function onAfterRender()
	{
		$app =jFactory::getApplication();
		if ($app->isSite() == false)
		{
			return;
		}
		$cookieid= $this->params->get('domain_group_id');
		$tochangescripts = $this->params->get('to_change_scripts');
		$enable_cb = $this->params->get('enable_cd');
		
		
		$body = $app->getBody();
		
		$cookie_script = sprintf('<head><script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="%s" type="text/javascript" async></script>', $cookieid);
		$cookie_declarion = sprintf('<script id="CookieDeclaration" src="https://consent.cookiebot.com/%s/cd.js" type="text/javascript" async></script>', $cookieid);
		
		$body = str_replace('<head>', $cookie_script , $body);
		
		$src = "core.js";
		$cookie_type = "marketing";
		
		if ($tochangescripts != NULL) {
			$tochangescripts  = explode("\n", $tochangescripts);
			$check = array();
			$step = 0;
			$last = count($tochangescripts);
			$last--;
			
			foreach($tochangescripts as $key=>$item){

				
				foreach(explode('|',$item) as $value){
					$check[$step++][$key] = $value;
			   	}
				$step = 0;
			}
		
			$cookie_add_type = sprintf(' data-cookieconsent="%s">', $cookie_type);

			preg_match_all('#<script(.*?)<\/script>#is', $body, $matches);
			$aantal_scripts = count($matches[1]);
			foreach ($matches[1] as $value) {

				/* controleer of bescript aangepast moet wordenop basis van bestandsnaam */
				foreach($check[0] as $key=>$src){
					
					if (strpos($value, $src) == true) {
						$source = $src;
						
						$cookie_add_type = sprintf(' data-cookieconsent="%s">',$check[1][$key]);
					
						$replace = str_replace("text/javascript", "text/plain", $value );
						$replace = str_replace('>', $cookie_add_type, $replace);
						
						$body = str_replace($source, $replace, $body); 

					}
				}
			}
		}
		
		/* Check if a cookie declartion needs to be on this page */
		if ($enable_cb == true ) {
			$replace_phrase = '{' . $this->params->get('cd_phrase') . '}';
			$body = str_replace($replace_phrase, $cookie_declarion, $body);
			
		}
		$app->setBody($body);
		
	}
}
