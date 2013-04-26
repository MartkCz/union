<?php

// Nette Framework TweetReader example

use Nette\Application\Routers\Route,
	Nette\Utils\Json;


// Load Nette Framework or autoloader generated by Composer
require __DIR__ . '/data/libs/autoload.php';

// Configure application
date_default_timezone_set('Europe/Prague');
$configurator = new Nette\Configurator;

// Enable Nette Debugger for error visualisation & logging
$configurator->enableDebugger(__DIR__ . '/data/log');

// Create Dependency Injection container
$configurator->setTempDirectory(__DIR__ . '/data/temp');
$container = $configurator->createContainer();

// Setup router using mod_rewrite detection
$container->router[] = new Route('[index.php]', function() {
	return 'Hello! Would you like to search <a href="search/nettefw">#nettefw</a> on Twitter?';
});

$container->router[] = new Route('search/<hashtag \w+>', function($hashtag, $presenter) {
	$params['response'] = Json::decode(file_get_contents('http://search.twitter.com/search.json?q=' . urlencode("#$hashtag")));
	return array('
		<h1>Results for #{$hashtag}</h1>

		{foreach $response->results as $item}
			<p><img src="{$item->profile_image_url}" width="48"><em>{$item->from_user}</em>: {$item->text}
				<small>at {$item->created_at|date:"j.n.Y H:i"}</small></p>
		{/foreach}
	', $params);
});

// Run the application!
$container->application->run();
