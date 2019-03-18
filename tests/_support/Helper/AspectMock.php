<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use AspectMock\Test;
use Aws\Result;
use Codeception\Stub;
use Codeception\TestInterface;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\Clients\SqsClient;

class AspectMock extends \Codeception\Module
{
    public function _initialize()
    {
        $kernel = \AspectMock\Kernel::getInstance();
        $kernel->init([
            'debug' => true,
            'includePaths' => [getcwd() . '/src'],
            'cacheDir'  => '/tmp/tests',
        ]);
    }

    public function _after(TestInterface $test)
    {
        Test::clean();
    }

    public function mockBuilderFacadeBuildActionFor(string $action, array $stubs = [])
    {
        $beforeAction = function ($headers, $consumeMessage) {
            $consumeMessage();
        };
        $mockedActionFunctions = ['beforeAction' => $beforeAction];

        if (!empty($stubs)) {
            $mockedActionFunctions = $stubs;
        }

        $action = Stub::makeEmpty($action, $mockedActionFunctions);
        $mock = function () use ($action) {
            return $action;
        };

        Test::double(BuilderFacade::class, ['buildActionFor' => $mock]);
    }

    public function mockSqsClientInstance($messages)
    {
        $result = Stub::make(Result::class, ['get' => $messages]);
        $client = Stub::makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
    }

    /**
     * @param string|array $classes
     */
    public function mockGetAction($classes)
    {
        Test::double(Constant::class, ['getAction' => $classes]);
    }
}
