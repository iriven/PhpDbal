<?php
/**
 * Created by PhpStorm.
 * User: sjhc1170
 * Date: 11/07/2016
 * Time: 14:15
 */

namespace Iriven\Libs;
use PDO;
use Iriven\Core\Expressions\ExpressionBuilder;
use Iriven\Core\QueryBuilderBase;
use Iriven\Libs\DatabaseConfiguration;

/**
 * Class DatabaseConnexion
 * @package Ressources\Systems\Kernels\Database
 */
class DatabaseConnexion
{
    /**
     * @var mixed
     */
    private $configuration;
    /**
     * @var
     */
    private $PDOInstance;
    private $PDODriver;

    /**
     * DatabaseConnexion constructor.
     * @param DatabaseConfiguration $config
     */
    public function __construct(DatabaseConfiguration $config)
    {
        if(!$config instanceof DatabaseConfiguration)
            return false;
        $this->configuration = $config->getParams();
        try{
            if(!class_exists('PDO',false))
                throw new \Exception('L\'Extension PDO de PHP est requise pour se connecter à la base de données.');
            $this->PDODriver = $this->getName($this->configuration['driver']);
            if(!in_array($this->PDODriver,PDO::getAvailableDrivers()))
                throw new \Exception('L\'Extension PDO '. $this->PDODriver.' n\'est pas installée sur ce serveur');
            $this->loadDatabase();
        }
        catch (\Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $this->PDOInstance;
    }

    /**
     * @return null|string
     */
    private function buidDsn()
    {
        if(isset($this->configuration['dsn']))
            return $this->configuration['dsn'];
        $dsn = null;
        switch ($this->PDODriver):
            case 'sqlsrv':
                    $dsn = 'sqlsrv:server=';
                    if (isset($this->configuration['host']))
                        $dsn .= $this->configuration['host'];
                    if (isset($this->configuration['port']) && !empty($this->configuration['port']))
                        $dsn .= ',' . $this->configuration['port'];
                    if (isset($this->configuration['dbname']))
                        $dsn .= ';Database=' .  $this->configuration['dbname'];
                    if (isset($this->configuration['MultipleActiveResultSets']))
                        $dsn .= '; MultipleActiveResultSets=' . ($this->configuration['MultipleActiveResultSets'] ? 'true' : 'false');
                 break;

            case 'dblib':
                    $dsn = 'dblib:host=';
                    if (isset($this->configuration['host']))
                        $dsn .= $this->configuration['host'];
                    if (isset($this->configuration['port']) && !empty($this->configuration['port']))
                        $dsn .= ':' . $this->configuration['port'];
                    if (isset($this->configuration['dbname']))
                        $dsn .= ';dbname=' .  $this->configuration['dbname'];

                break;
            
            case 'sqlite':
                $dsn = 'sqlite:';
                if (isset($this->configuration['path'])) $dsn .= $this->configuration['path'];
                elseif (isset($this->configuration['memory'])) 
                    $dsn .= ':memory:';
                break;
            
            case 'pgsql':
                $dsn = 'pgsql:';
                if (isset($this->configuration['host']) && $this->configuration['host'] != '') 
                    $dsn .= 'host=' . $this->configuration['host'] . ' ';
                if (isset($this->configuration['port']) && $this->configuration['port'] != '')
                    $dsn .= 'port=' . $this->configuration['port'] . ' ';
                if (isset($this->configuration['dbname']))
                    $dsn .= 'dbname=' . $this->configuration['dbname'] . ' ';
                else
                    // Used for temporary connections to allow operations like dropping the database currently connected to.
                    // Connecting without an explicit database does not work, therefore "template1" database is used
                    // as it is certainly present in every server setup.
                    $dsn .= 'dbname=template1' . ' ';
                if (isset($this->configuration['sslmode'])) 
                    $dsn .= 'sslmode=' . $this->configuration['sslmode'] . ' ';
                break;
            
            case 'oci':
                $dsn = 'oci:dbname=' . $this->getOracleConnectString();
                if (isset($this->configuration['charset']))
                    $dsn .= ';charset=' . $this->configuration['charset'];
                break;

            case 'ibm':
                $dsn = 'ibm:DRIVER={IBM DB2 ODBC DRIVER};';
                if (isset($this->configuration['host'])) 
                    $dsn .= 'HOSTNAME=' . $this->configuration['host'] . ';';
                if (isset($this->configuration['port'])) 
                    $dsn .= 'PORT=' . $this->configuration['port'] . ';';
                $dsn .= 'PROTOCOL=TCPIP;';
                if (isset($this->configuration['dbname'])) 
                    $dsn .= 'DATABASE=' . $this->configuration['dbname'] . ';';
                break;
            
            default:
                $dsn = 'mysql:';
                if (isset($this->configuration['host']) && $this->configuration['host'] != '')
                    $dsn .= 'host=' . $this->configuration['host'] . ';';
                if (isset($this->configuration['port']))
                    $dsn .= 'port=' . $this->configuration['port'] . ';';
                if (isset($this->configuration['dbname']))
                    $dsn .= 'dbname=' . $this->configuration['dbname'] . ';';
                if (isset($this->configuration['unix_socket']))
                    $dsn .= 'unix_socket=' . $this->configuration['unix_socket'] . ';';
                if (isset($this->configuration['charset']))
                    $dsn .= 'charset=' . $this->configuration['charset'] . ';';
                break;
            endswitch;
        return $dsn;
    }

    /**
     * @return mixed|\PDO
     */
    private function loadDatabase()
    {
        if (!$this->PDOInstance)
        {
            try{
                $dsn = $this->buidDsn();
                $Options = $this->resolveOptions();
                if(!$this->PDOInstance = new PDO($dsn,$this->configuration['user'],$this->configuration['password'],$Options['attr']))
                    throw new \PDOException('Impossible de se connecter à la base de données');
                if(count($Options['cmd']) > 0)
                {
                    foreach ($Options['cmd'] as $cmd)
                        $this->PDOInstance->exec($cmd);
                }
            }
            catch (\PDOException $e){trigger_error($e->getMessage(), E_USER_ERROR);}
        }
        return $this->PDOInstance;
    }

    /**
     * @return mixed
     */
    private function resolveOptions(){
        $Options = [
                        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES => true,
                        PDO::ATTR_PERSISTENT => true,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                    ];
        $Command = [];
        $Params = $this->configuration;
        if($this->getName($Params['driver'])==='mysql')
        {
            if( defined('PDO::MYSQL_ATTR_INIT_COMMAND') )
                $Options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES '.$Params['charset'];
            
            $Command[] = 'SET SQL_MODE=ANSI_QUOTES';
            $Options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = false;
            $Options[PDO::MYSQL_ATTR_COMPRESS] = true;
        }

        if($Params['fetchmode'] !== 'object')
            $Options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;

        if(!$Params['persistent'])
            $Options[PDO::ATTR_PERSISTENT] = false;

        if(!$Params['prepare'])
            $Options[PDO::ATTR_EMULATE_PREPARES] = false;

        if(!isset($Options[PDO::MYSQL_ATTR_INIT_COMMAND]) AND ($this->getName($Params['driver'])!=='oci'))
            $Command[] = 'SET NAMES '.$Params['charset'];

        if($this->getName($Params['driver']) ==='sqlsrv')
            $Command[] = 'SET QUOTED_IDENTIFIER ON';

        return ['attr'=>$Options,'cmd'=>$Command];
    }
    /**
     * @return mixed
     */
    public function ServerVersion()
    {
        if(!$this->PDOInstance instanceof PDO)
            return false;
        return $this->PDOInstance->getAttribute(PDO::ATTR_SERVER_VERSION);
    }
    /**
     * Returns an appropriate Easy Connect String for the given parameters.
     * @return string
     */
    private function getOracleConnectString()
    {
        if ( ! empty($this->configuration['host'])) 
        {
            if ( ! isset($this->configuration['port']))
                $this->configuration['port'] = 1521;
            $serviceName = $this->configuration['dbname'];
            if ( ! empty($this->configuration['servicename']))
                $serviceName = $this->configuration['servicename'];
            $service = 'SID=' . $serviceName;
            $pooled  = '';
            $instance = '';
            if (isset($this->configuration['service']) && $this->configuration['service'] == true)
                $service = 'SERVICE_NAME=' . $serviceName;
            if (isset($this->configuration['instancename']) && ! empty($this->configuration['instancename']))
                $instance = '(INSTANCE_NAME = ' . $this->configuration['instancename'] . ')';
            if (isset($this->configuration['pooled']) && $this->configuration['pooled'] == true)
                $pooled = '(SERVER=POOLED)';
            return '(DESCRIPTION=' .
            '(ADDRESS=(PROTOCOL=TCP)(HOST=' . $this->configuration['host'] . ')(PORT=' . $this->configuration['port'] . '))' .
            '(CONNECT_DATA=(' . $service . ')' . $instance . $pooled . '))';
        }
        return isset($this->configuration['dbname']) ? $this->configuration['dbname'] : '';
    }

    /**
     * @param $driver
     * @return string
     */
    public function getName($driver){
        if(!$driver) $driver='mysql';
        $driver = strtolower($driver);
            switch ($driver)
            {
                case (strpos($driver,'mssql')):
                case (strpos($driver,'sqlserver')):
                case (strpos($driver,'sqlsrv')):
                    $driver = (strpos(PHP_OS, 'WIN') !== false )? 'sqlsrv' : 'dblib';
                    break;
                case (strpos($driver,'sybase')):
                    $driver = 'dblib';
                    break;
                case (strpos($driver,'pgsql')):
                    $driver = 'pgsql';
                    break;
                case (strpos($driver,'sqlite')):
                    $driver = 'sqlite';
                    break;
                case (strpos($driver,'ibm')):
                case (strpos($driver,'db2')):
                case (strpos($driver,'odbc')):
                    $driver = 'ibm';
                    break;
                case (strpos($driver,'oracle')):
                    $driver = 'oci';
                    break;
                default:
                    $driver = 'mysql';
                    break;
            }
        return $driver;
    }

    /**
     * @return ExpressionBuilder
     */
    public function getExpressionBuilder()
    {
        return new ExpressionBuilder($this);
    }

    /**
     * @return IrivenPHPQueryBuilder
     */
    protected function loadQueryBuilder()
    {
        return new QueryBuilderBase($this);
    }

    /**
     * @return PDO
     */
    public function getConnex()
    {
        try{
            if (!$this->PDOInstance instanceof \PDO)
                throw new \Exception('Aucune connexion n\'a été établie avec la base de donnée.');
        }
        catch(\Exception $a){
            trigger_error($a->getMessage(), E_USER_ERROR);
        }
        return $this->PDOInstance;
    }

    /**
     * @param $value
     * @return array|string
     */
    public function quote($value)
    {
        try{
            if (!$this->PDOInstance instanceof \PDO)
                throw new \Exception('Aucune PDOInstanceion n\'a été établie avec la base de donnée.');
            if(is_array($value))
            {
                $return=[];
                foreach ($value as $col=>$_)
                    $return[$col]=call_user_func([$this,__METHOD__],$_);
                return $return;
            }
            if(is_numeric($value)&& !is_string($value)) return (string) $value;
            if(is_bool($value)) return $value? 1 : 0;
        }
        catch(\Exception $a){
            trigger_error($a->getMessage(), E_USER_ERROR);
        }
        return $this->PDOInstance->quote($value);
    }
}
