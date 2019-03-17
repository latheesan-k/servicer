<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use AspectMock\Kernel;
use AspectMock\Test;
use Codeception\Stub;
use Codeception\TestInterface;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Actions\Constant;

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

    public function mockBuildActionFor(array $stubs = [])
    {
        $skipMessage = function ($headers, $consumeMessage) {
            $consumeMessage();
        };
        $mockedActionFunctions = ['skipMessage' => $skipMessage];

        if (!empty($stubs)) {
            $mockedActionFunctions = $stubs;
        }

        $action = Stub::makeEmpty(ActionInterface::class, $mockedActionFunctions);
        $mock = function () use ($action) {
            return $action;
        };

        Test::double(BuilderFacade::class, ['buildActionFor' => $mock]);
    }

    /**
     * @param string|array $classes
     */
    public function mockGetAction($classes)
    {
        Test::double(Constant::class, ['getAction' => $classes]);
    }
}
