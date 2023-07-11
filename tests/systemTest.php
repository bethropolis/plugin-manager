<?php


require_once 'src/PluginSystem.php';

use Bethropolis\PluginSystem\System;
use PHPUnit\Framework\TestCase;

class SystemTest extends TestCase
{
    public function testSetPluginsDir()
    {
        $dir = __DIR__ . "/../examples/";
        System::setPluginsDir($dir);
        print_r(System::getPluginsDir());

        $this->assertSame($dir, System::getPluginsDir());
    }
    public function testLoadPlugins()
    {
        $dir = __DIR__ . "/../examples/";
        System::setPluginsDir($dir);
        $load = System::loadPlugins();

        $this->assertTrue($load);
    }


    public function testExecution()
    {
        $dir = __DIR__ . "/../examples/";
        System::setPluginsDir($dir);
        $load = System::loadPlugins();
        $this->assertTrue($load);

        $item = System::executeHook('my_hook', "Bethropolis\PluginSystem\MyPlugin\Load", "john");
        $this->assertSame($item, "hello john");
    }
    

 public function testExecutions()
    {
        $dir = __DIR__ . "/../examples/";
        System::setPluginsDir($dir);
        $load = System::loadPlugins();

        $items = System::executeHooks(["other_hook","test_hook"],"Bethropolis\PluginSystem\AnotherPlugin\Load");
        $this->assertIsArray($items);
    }
}
