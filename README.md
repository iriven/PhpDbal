# Iriven PhpDbal

A PHP multi driver Database Abstarction Layer.


## Requirements

- [x] php_pdo

```php
/*
* NOTE: The PDO extension of PHP must be installed and active for you to use this class.
* The same is true of the PDO modules of each driver you used in your project (eg pdo_mysql for MySQL)
*/
```
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
### Compatibility:
This project supports most of the most well-known database management systems including:
- [x] MySQL
- [x] SQLite
- [x] PgSQL
- [x] Oracle
- [x] SQLServer
- [x] SQLsrv
- [x] MsSQL
- [x] DB2
- [x] IBM
- [x] ODCB
- [x] Sysbase

## Authors

* **Alfred TCHONDJO** - *Project Initiator* - [iriven France](https://www.facebook.com/Tchalf)

## License

This project is licensed under the GNU General Public License V3 - see the [LICENSE](LICENSE) file for details

## Disclaimer

If you use this library in your project please add a backlink to this page by this code.

```html

<a href="https://github.com/iriven/PhpDbal" target="_blank">This Project Uses Alfred's TCHONDJO  PHPDbal Library.</a>
```
