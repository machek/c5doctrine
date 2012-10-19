<?php

(@include_once __DIR__ . '/vendor/autoload.php');

use Doctrine\ORM\EntityManager,
	Doctrine\Common\Annotations\AnnotationRegistry,
	Doctrine\ORM\Configuration;

class DoctrineC5Adapter {

	private static $entityManager = false;
	private static $initialized = false;

	private static function initialize() {
		$config = new \Doctrine\ORM\Configuration();
		$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);

		AnnotationRegistry::registerFile(
				__DIR__ . "/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
		AnnotationRegistry::registerAutoloadNamespace(
				'Symfony\\Component\\Validator', __DIR__ . '/vendor/symfony/validator/');

		$reader = new Doctrine\Common\Annotations\AnnotationReader();
		$driverImpl = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, array(__DIR__ . "/src/Collapick/Models"));
		$config->setMetadataDriverImpl($driverImpl);


		$config->setProxyDir(__DIR__ . '/src/Proxies');
		$config->setProxyNamespace('Proxies');

// Parse with sections
		$ini_array = parse_ini_file("doctrine.ini", true);
		$parameters = $ini_array['parameters'];


		$connectionOptions = array(
			'driver' => $parameters['database_driver'],
			'dbname' => $parameters['database_name'],
			'user' => $parameters['database_user'],
			'password' => $parameters['database_password'],
			'host' => $parameters['database_host']);

		$em = EntityManager::create($connectionOptions, $config);

		if (self::$initialized)
			return;

		self::$entityManager = $em;
		self::$initialized = true;
	}

	/**
	 * 
	 * @return EntityManager
	 */
	public static function getEntityManager() {
		self::initialize();
		return self::$entityManager;
	}

}
