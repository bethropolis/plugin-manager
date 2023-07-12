# a PHP Plugin System

This is a lightweight and flexible plugin management system.
It allows you to easily integrate plugins into your PHP applications, providing a modular and extensible architecture.

## Features

- Easy integration: The plugin system is designed to be easily integrated into your existing PHP projects.

- Dynamic loading: Plugins can be loaded dynamically from a specified directory.

- Hook-based architecture: Plugins can be linked to hooks, allowing them to execute actions when specific events occur.

- Event-driven programming: Plugins can register events and define actions to be executed when those events are triggered.

- Flexible and extensible: The system provides a flexible and extensible architecture, allowing you to add and manage plugins according to your application's needs.


## Installation

you will require composer to install. Run the following command in your project directory:
```php
composer require bethropolis/plugin-system
```

## Usage

### Loading Plugins
To load plugins from a specific directory, use the `loadPlugins` method:


```php

require "vendor/autoload.php";

use Bethropolis\PluginSystem\System;

$dir = __DIR__ . "/examples/"; # directory to load plugins from
System::loadPlugins($dir);

```

## Linking Plugins to Hooks
Plugins can be linked to hooks using the `linkPluginToHook` method. This allows you to define actions that will be executed when a particular hook is triggered:
```php
System::linkPluginToHook('my_hook', $callback);
```

### Triggering Hooks and Events
Hooks can be triggered using the `executeHook()` method, and events can be triggered using the `triggerEvent()` method. Here's an example:

```php
use Bethropolis\PluginSystem\System;

// Trigger a hook
System::executeHook('my_hook', $pluginName, ...$args);

// trigger multiple hooks
System::executeHooks(['my_hook1', 'my_hook2'], $pluginName, ...$args);

# Events
// Register an event
System::registerEvent('my_event');

// Add an action to the event
System::addAction('my_event', function ($arg) {
    // Action code here
});

// Trigger the event
System::triggerEvent('my_event', ...$args);
```

## Examples

The examples directory contains sample plugins that demonstrate the usage of the Plugin System. 

## Contributing

Contributions to the project are welcome! If you encounter any issues, have suggestions for improvements, or would like to add new features, please feel free to open an issue or submit a pull request.


## About

this project was made to be a plugin management system for another one of my [project](https://github.com/bethropolis/suplike-social-website) but I hope it can help someone else out there.

## License

this project is released under the [MIT License](https://opensource.org/licenses/MIT). You can find more details in the [LICENSE](https://chat.openai.com/LICENSE) file.