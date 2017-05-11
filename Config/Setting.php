<?php
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
