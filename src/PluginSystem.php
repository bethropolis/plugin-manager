<?php

namespace Bethropolis\PluginSystem;

use Bethropolis\PluginSystem\Loader;


class System
{
    private static $plugins = array();
    private static $pluginsDir;

    private static $events = array();

    /**
     * Check if a plugin class exists.
     *
     * @param string $className The name of the class to check.
     * @return bool Returns true if the class exists, false otherwise.
     */
    private static function pluginClassExists($className)
    {
        return class_exists($className);
    }

    public static function setPluginsDir($dir)
    {
        self::$pluginsDir = $dir;
    }
    public static function getPlugins()
    {
        return self::$plugins;
    }

    public static function getPluginsDir()
    {
        return self::$pluginsDir;
    }


    /**
     * Load plugins from a specified directory.
     *
     * @param string|null $dir The directory path to load plugins from. If null, uses the default plugins directory.
     * @return bool Returns true if the plugins are successfully loaded.
     */
    public static function loadPlugins($dir = null)
    {
        if ($dir) {
            self::setPluginsDir($dir);
        }
        $pluginsDir = self::$pluginsDir;

        foreach (new \DirectoryIterator($pluginsDir) as $folder) {
            if (!$folder->isDot() && $folder->isDir()) {
                $pluginFile = $pluginsDir . $folder->getFilename() . '/plugin.php';

                if (file_exists($pluginFile)) {
                    $classAutoloader = function ($className) use ($pluginsDir, $folder) {
                        Loader::pluginClassAutoloader($className, $pluginsDir, $folder->getFilename());
                    };

                    spl_autoload_register($classAutoloader);

                    Loader::pluginAutoloader($pluginFile);

                    $pluginClass = __NAMESPACE__ . '\\' . $folder->getFilename() . 'Plugin\\Load';
                    if (self::pluginClassExists($pluginClass)) {
                        $pluginInstance = new $pluginClass();
                        $pluginInstance->setupHooks();
                        $pluginInstance->getInfo();
                    }

                    spl_autoload_unregister($classAutoloader);
                }
            }
        }
        return true;
    }


    /**
     * Link a plugin to a hook.
     *
     * @param mixed $hook     The hook to link the plugin to.
     * @param mixed $callback The callback function to be executed when the hook is triggered.
     *
     * @return void
     */
    public static function linkPluginToHook($hook, $callback)
    {
        if (!isset(self::$plugins[$hook])) {
            self::$plugins[$hook] = array();
        }

        self::$plugins[$hook][] = $callback;
    }

    /**
     * Executes a hook by calling all registered callbacks associated with it.
     *
     * @param string $hook The name of the hook to execute.
     * @param string|null $pluginName The name of the plugin. Default is null.
     * @param mixed ...$args The arguments to pass to the callbacks.
     * @return array The return values from the callbacks.
     */
    public static function executeHook($hook, $pluginName = null, ...$args)
    {
        $returnValues = array();

        if (isset(self::$plugins[$hook])) {
            foreach (self::$plugins[$hook] as $callback) {
                $callbackPluginName = get_class($callback[0]);

                if ($pluginName === null || $pluginName === $callbackPluginName) {
                    $returnValue = call_user_func($callback, $args);
                    if ($returnValue !== null &&  $pluginName === null) {
                        $returnValues[] = $returnValue;
                    } else {
                        $returnValues = $returnValue;
                    }
                }
            }
        }

        return $returnValues;
    }

    /**
     * Executes a series of hooks.
     *
     * @param array $hooks An array of hooks to execute.
     * @param string|null $pluginName The name of the plugin. Defaults to null.
     * @param mixed ...$args Additional arguments to pass to the hooks.
     * @return array An array of return values from the executed hooks.
     */
    public static function executeHooks(array $hooks, $pluginName = null, ...$args)
    {

        $returnValues = array();
        foreach ($hooks as $hook) {
            $returnValue = self::executeHook($hook, $pluginName, ...$args);
            if (!empty($returnValue)) {
                $returnValues[$hook] = $returnValue;
            }
        }
        return $returnValues;
    }

    /**
     * Registers an event.
     *
     * @param mixed $eventName The name of the event to register.
     */
    public static function registerEvent($eventName)
    {
        if (!isset(self::$events[$eventName])) {
            self::$events[$eventName] = array();
        }
    }

    /**
     * Adds an action to the event specified by $eventName.
     *
     * @param mixed $eventName The name of the event.
     * @param mixed $callback The callback function to be executed when the event is triggered.
     * @return void
     */
    public static function addAction($eventName, $callback)
    {
        if (isset(self::$events[$eventName])) {
            self::$events[$eventName][] = $callback;
        }
    }

 
    /**
     * Triggers an event and calls all registered callbacks for that event.
     *
     * @param string $eventName The name of the event to trigger.
     * @param mixed ...$args Additional arguments to pass to the callbacks.
     * @return array The return values from the callbacks, if any.
     */
    public static function triggerEvent($eventName, ...$args)
    {
        $returnValues = array();
        if (isset(self::$events[$eventName])) {
            foreach (self::$events[$eventName] as $callback) {
              $returnValue = call_user_func_array($callback, $args);
              if ($returnValue !== null) {
                $returnValues[] = $returnValue;
              }
            }
        }
        return $returnValues;
    }
}
