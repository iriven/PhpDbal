# Iriven PhpDbal

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)

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

To utilize PhpDbal, first import and require PhpDBAL.php file in your project.
##### Installation
```php
require_once 'PhpDBAL.php';
```
##### Example Configuration File (setting.php)
The configuration file must adhere to the psr-4 standard:
```php
return  array (
      'default' =>
          array (
              'driver' => 'mysql',
              'host' => 'localhost1',
              'dbname' => 'mydatabase1',
              'user' => 'myusername1',
              'password' => 'mypassword',
              'prefix'=>'DB1_',
              'port' => 3306,
              'persistent' => 1,
              'fetchmode' => 'object',
              'prepare' => 1
          ),
      'project2' =>
          array (
              'driver' => 'pgsql',
              'host' => 'localhost2',
              'dbname' => 'mydatabase2',
              'user' => 'myusername2',
              'password' => 'mypassword',
              'port' => 5432
          )
  );
```
##### Initialisation
```php

$DBInstance = new Iriven\PhpDBAL(
                                  new Iriven\Libs\DatabaseConfiguration($PoolName='default')
                                  ); //Initialisation
/* 
* NOTE: to load a different database instance, just change the "poolname" value. 
* According to the setting.php file content, you can set the poolname value to "project2",
* in order to connect to the second database.
*/
```
#### QueryBuilder Usage

##### - Basic Usage:
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
 else 
    print_r($Members);
```    
Insert a new User or Update user data:
```php
      $data = [
                  'id'=>null,
                  'username'=>null,
                  'email'=>null,
                  'password'=>null,
                  'activationKey'=>null,
                  'banned'=>null,
                  'active'=>null
              ];
      extract($data);
      $values = [];
      $params = [];
      if(!empty($username))   { $values['u.usersusername']        = ':pseudo';   $params[':pseudo'] = $username;}
      if(!empty($email))      { $values['u.usersemail']           = ':email';    $params[':email']  = $email;}
      if(!empty($password))   { $values['u.userspassword']        = ':passwd';    $params[':passwd']  = $password;}
      if(!empty($token))      { $values['u.usersactivationkey']   = ':token';    $params[':token'] = $token;}
      if(!empty($banned))     { $values['u.usersisactive']        = ':banned';   $params[':banned'] = $banned;}
      if(!empty($active))     { $values['u.usersisactive']        = ':active';   $params[':active'] = $active;}
      if(!empty($id) and $id>=1)
      {
          $QueryBuilder = $this->QueryBuilder()
              ->update('users','u')
              ->where('u.idusers = :uid')
              ->setParameter(':uid',$id);
          if($values)
              $QueryBuilder->set($values)->setParameters($params);
      }
      else
      {
          $QueryBuilder = $this->QueryBuilder()
              ->insert('users u')
              ->values($values)
              ->setParameters($params);
      }
      $uid = $QueryBuilder->execute(); 
```
##### - Advanced Usage:

retrieve user login informations. here a user can login using a couple of (username + password) or (email + password)
```php
$data = [
            'uid'=>null,
            'username'=>null,
            'email'=>null,
            'banned'=>false,
            'active'=>1
        ];
        extract($data);
        $clause = [];
        $params = [];
        $User = $DBInstance->QueryBuilder()
                ->select('u.id AS id, u.username AS username, u.password AS password, u.email AS email, u.isactive AS active, u.isbanned AS banned')
                ->from('users','u');
            if(!empty($username))
            {
                $expr = $User->expr();
                $clause[] = $expr->orX($expr->eq('username',':pseudo'),$expr->eq('email',':pseudo'));   
                $params[':pseudo'] = $username;
            }
            if(!empty($uid))        { $clause[] = 'id = :uid';                      $params[':uid']    = $uid;}
            if(!empty($banned))     { $clause[] = 'banned= :banned';                $params[':banned'] = $banned;}
            if(!empty($active))     { $clause[] = 'active = :active';               $params[':active'] = $active;}
            if($clause)
                $User->where(implode(' AND ',$clause))->setParameters($params);
            if(!$User->execute())
                echo 'User not found';
            else 
                  print_r($User);
```
### Compatibility:
This project handles most of the well-known database vendors including:
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

## Donation

If this project help you reduce time to develop, you can give me a cup of coffee :)

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)

## Disclaimer

If you use this library in your project please add a backlink to this page by this code.

```html

<a href="https://github.com/iriven/PhpDbal" target="_blank">This Project Uses Alfred's TCHONDJO  PHPDbal Library.</a>
```
## Issues Repport
Repport issues [Here](https://github.com/iriven/PhpDbal/issues)
