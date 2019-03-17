<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    public function expectExceptionMessage($message, $function)
    {
        try {
            $function();
        } catch (\Exception $e) {
            $this->assertContains($message, $e->getMessage());
        }
    }
}
