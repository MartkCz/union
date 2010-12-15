<?php

use Nette\Debug,
	Nette\Environment,
	Nette\Application\Route,
	Nette\Application\SimpleRouter;



// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR . '/Nette/Nette/loader.php';



// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Debug::enable();

// 2b) load configuration from config.ini file
Environment::loadConfig();



// Step 3: Configure application
// 3a) get and setup a front controller
$application = Environment::getApplication();

// 3b) establish database connection
$application->onStartup[] = function() {
	Model::initialize(Environment::getConfig()->database);
};



// Step 4: Setup application router
$router = $application->getRouter();

// mod_rewrite detection
if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
	$router[] = new Route('index.php', 'Dashboard:default', Route::ONE_WAY);

	$router[] = new Route('<presenter>/<action>[/<id>]', 'Dashboard:default');

} else {
	$router[] = new SimpleRouter('Dashboard:default');
}



// Step 5: Run the application!
$application->run();
