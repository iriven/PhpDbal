# Iriven PhpDbal

A PHP multi driver Database Abstarction Layer.


## Requirements

- [x] php_pdo

## Dependencies

- [x] [Iriven\ConfigManager](https://github.com/iriven/ConfigManager)
- [x] [Iriven\OptionsResolver](https://github.com/iriven/PhpOptionsResolver)

## Usage:


### Installation And Initialisation

To utilize GeoIPCountry, first import and require PhpDBAL.php file in your project.

```php
require_once 'PhpDBAL.php';
$DBInstance = new Iriven\PhpDBAL(new DatabaseConfiguration($PoolName='default')); //Initialisation
/* 
* NOTE: to load a different database instance, just change the "poolname" value. 
* According to the setting.php file content, you can set the poolname value to "project2",
* in order to connect to the second database.
*/
```
