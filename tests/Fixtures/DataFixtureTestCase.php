<?php

namespace App\Tests\Fixtures;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Client;

class DataFixtureTestCase extends WebTestCase
{
    /** @var  Application $application */
    protected static $application;

    /** @var  Client $client */
    protected $client;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load --append --no-interaction');

        $this->client = static::createClient();

        parent::setUp();
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    /**
     * @param $uri
     * @param $method
     * @param $headers
     * @param $parameters
     * @return Response
     */
    protected function request($uri, $method, $headers = array(), $parameters = null)
    {
        array_push($headers, ['CONTENT_TYPE'=> 'application/json']);
        $this->client->request($method, $uri, [], [], $headers, $parameters);

        return $this->client->getResponse();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        self::runCommand('doctrine:database:drop --force');

        parent::tearDown();
    }

    /**
     * @return mixed
     */
    protected function responseBody()
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}