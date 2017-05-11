<?php
/**
 * Created by PhpStorm.
 * User: Iriven
 * Date: 11/07/2016
 * Time: 23:04
 */

namespace Iriven;

use Iriven\Libs\DatabaseConfiguration;
use Iriven\Libs\DatabaseConnexion;

/**
 * Class PhpDBAL
 * @package Iriven\PhpDBAL
 */

class PhpDBAL extends DatabaseConnexion
{
    /**
     * PhpDBAL constructor.
     * @param DatabaseConfiguration $config
     */
    public function __construct(DatabaseConfiguration $config)
    {
        parent::__construct($config);
        return $this;
    }

    /**
     * @return Database\Builders\IrivenPHPQueryBuilder
     */
    public function QueryBuilder()
    {
        return $this->loadQueryBuilder();
    }
}
