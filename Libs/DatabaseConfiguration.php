<?php
/**
 * Created by PhpStorm.
 * User: sjhc1170
 * Date: 11/07/2016
 * Time: 14:17
 */

namespace Iriven\Libs;

use Iriven\ConfigManager;
use Iriven\PhpOptionsResolver;
use Iriven\DataCollector;

final class DatabaseConfiguration
{
    private $dbPoolName='default';
    private $dbConfig;
    /**
     * @param null $poolName
     */
    public function __construct($poolName=null)
    {
        $this->setPoolName($poolName);
        $this->dbConfig = new DataCollector();
        $this->load();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParams(){
        return $this->dbConfig->get($this->dbPoolName,[]);
    }

    /**
     * @return array|null
     */
    private function load(){
        try{
            $DS = DIRECTORY_SEPARATOR;
            $dbCfgFile = dirname(__DIR__).$DS.'Config'.$DS.'setting.php';
            if(!$setup = new Iriven\ConfigManager($dbCfgFile))
                throw new \Exception('Paramètres de connexion à la base de données introuvables');
            $dbParams = array_change_key_case($setup->get($this->dbPoolName),CASE_LOWER);
           $dbParams['port'] = $this->setPort($dbParams['driver'],$dbParams['port']?:null);
            if(isset($dbParams['persistent']))
            $dbParams['persistent'] = $dbParams['persistent']? true:false;
            if(isset($dbParams['prepare']))
            $dbParams['prepare'] = $dbParams['prepare']? true:false;
            $OptionResolver = new PhpOptionsResolver();
            $OptionResolver->setDefaults([
                'driver'=>'mysql',
                'charset'=>'utf8',
                'host'=>'localhost',
                'dbname'=>null,
                'port'=>3306,
                'password'=>null,
                'user'=>null,
                'prefix'=>'',
                'persistent'=>true,
                'fetchmode'=>'object',
                'prepare'=>true
            ]);
            $OptionResolver->setRequired(['dbname','driver','host','password','user']);
            $OptionResolver->setAllowedValues('fetchmode',['array','object']);
            $OptionResolver->addAllowedValues('driver',['mysql','pgsql','sqlite','oracle','sqlsrv','mssql','sqlserver','ibm','db2','sybase','odbc']);
            $OptionResolver->setAllowedTypes('port','integer');
            $OptionResolver->setAllowedTypes('persistent','bool');
            $OptionResolver->setAllowedTypes('prepare','bool');
            $dbParams = $OptionResolver->resolve($dbParams);
            $this->dbConfig->add([$this->dbPoolName=>$dbParams]);
            return $dbParams;
        }
        catch (\Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return null;
    }

    /**
     * @param $poolName
     * @return bool|string
     */
    private function setPoolName($poolName=null){
        $poolName = trim(strip_tags(strtolower($poolName)));
        if(!$poolName) return false;
        return $this->dbPoolName = $poolName;
    }

    /**
     * @param $driver
     * @param null $port
     * @return int|null
     */
    private function setPort($driver,$port=null)
    {
        if(!$port or !is_int($port*1)){
            switch($driver):
                case 'oracle':
                    $port = 1521;
                    break;
                case 'pgsql':
                    $port = 5432;
                    break;
                case 'ibm':
                case 'db2':
                case 'odbc':
                    $port = 50000;
                    break;
                case 'sqlsrv':
                case 'mssql':
                case 'sqlserver':
                case 'sybase':
                    $port = 1433;
                    break;
                default:
                    $port = 3306;
            endswitch;
        }
        return $port;
    }

}
