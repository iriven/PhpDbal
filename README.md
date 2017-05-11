# Iriven PhpDbal

A powerful PHP multi driver database Abstraction and Access Layer using PDO


## Requirements

- [x] php_pdo

```php
/*
* NOTE: To use this class, the PDO extension must be installed and active on your web server.
* The same is true of the PDO modules of each driver you used in your project (eg pdo_mysql for MySQL)
*/
```
## Dependencies

- [x] [Iriven\ConfigManager](https://github.com/iriven/ConfigManager)
- [x] [Iriven\OptionsResolver](https://github.com/iriven/PhpOptionsResolver)

## Usage:

#### Installation And Initialisation

To utilize GeoIPCountry, first import and require PhpDBAL.php file in your project.

```php
require_once 'PhpDBAL.php';
$DBInstance = new Iriven\PhpDBAL(
                                  new Iriven\Libs\DatabaseConfiguration($PoolName='default')
                                  ); //Initialisation
/* 
* NOTE: to load a different database instance, just change the "poolname" value. 
* According to the setting.php file content, you can set the poolname value to "project2",
* in order to connect to the second database.
*/
```
#### Build & execute Query
List all active members informations in the 'users' database table:
```php
$Members = $DBInstance->QueryBuilder()
                ->select()
                ->from('users','u')
                ->where('u.isactive = :active')
                ->andWhere('u.isbanned = :banned')
                ->setParameters([':active'=>1,':banned'=>0])
                ->execute();
 if(!$Members)
                echo  'No active member found';
   else        print_r($Members);
```

### Compatibility:
This project supports most of the well-known database vendors including:
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
