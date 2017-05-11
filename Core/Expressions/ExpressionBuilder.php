<?php
/**
 * Created by PhpStorm.
 * User: Iriven
 * Date: 16/07/2016
 * Time: 14:16
 */

namespace Iriven\Core\Expressions;

use Iriven\Libs\DatabaseConnexion;


/**
 * Class ExpressionBuilder
 * @package Iriven\Core\Expressions
 */
class ExpressionBuilder
{
    const EQ  = '=';
    const NEQ = '<>';
    const LT  = '<';
    const LTE = '<=';
    const GT  = '>';
    const GTE = '>=';

    /**
     * @var DatabaseConnexion
     */
    private $DBInstance;

    /**
     * ExpressionBuilder constructor.
     * @param DatabaseConnexion $connection
     */
    public function __construct(DatabaseConnexion $connection)
    {
        $this->DBInstance = $connection;
    }

    /**
     * Creates a conjunction of the given boolean expressions.
     *
     * Example:
     *
     *     [php]
     *     // (u.type = ?) AND (u.role = ?)
     *     $expr->andX('u.type = ?', 'u.role = ?'));
     *
     * @param mixed $x Optional clause. Defaults = null, but requires
     *                 at least one defined when converting to string.
     *
     * @return CompositeExpression
     */
    public function andX($x = null)
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
    }

    /**
     * Creates a disjunction of the given boolean expressions.
     *
     * Example:
     *
     *     [php]
     *     // (u.type = ?) OR (u.role = ?)
     *     $qb->where($qb->expr()->orX('u.type = ?', 'u.role = ?'));
     *
     * @param mixed $x Optional clause. Defaults = null, but requires
     *                 at least one defined when converting to string.
     *
     * @return CompositeExpression
     */
    public function orX($x = null)
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
    }

    /**
     * Creates a comparison expression.
     *
     * @param mixed  $x        The left expression.
     * @param string $operator One of the ExpressionBuilder::* constants.
     * @param mixed  $y        The right expression.
     *
     * @return string
     */
    public function comparison($x, $operator, $y)
    {
        return $x . ' ' . $operator . ' ' . $y;
    }

    /**
     * Creates an equality comparison expression with the given arguments.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> = <right expr>. Example:
     *
     *     [php]
     *     // u.id = ?
     *     $expr->eq('u.id', '?');
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function eq($x, $y)
    {
        return $this->comparison($x, self::EQ, $y);
    }

    /**
     * Creates a non equality comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <> <right expr>. Example:
     *
     *     [php]
     *     // u.id <> 1
     *     $q->where($q->expr()->neq('u.id', '1'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function neq($x, $y)
    {
        return $this->comparison($x, self::NEQ, $y);
    }

    /**
     * Creates a lower-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> < <right expr>. Example:
     *
     *     [php]
     *     // u.id < ?
     *     $q->where($q->expr()->lt('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function lt($x, $y)
    {
        return $this->comparison($x, self::LT, $y);
    }

    /**
     * Creates a lower-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <= <right expr>. Example:
     *
     *     [php]
     *     // u.id <= ?
     *     $q->where($q->expr()->lte('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function lte($x, $y)
    {
        return $this->comparison($x, self::LTE, $y);
    }

    /**
     * Creates a greater-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> > <right expr>. Example:
     *
     *     [php]
     *     // u.id > ?
     *     $q->where($q->expr()->gt('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function gt($x, $y)
    {
        return $this->comparison($x, self::GT, $y);
    }

    /**
     * Creates a greater-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> >= <right expr>. Example:
     *
     *     [php]
     *     // u.id >= ?
     *     $q->where($q->expr()->gte('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function gte($x, $y)
    {
        return $this->comparison($x, self::GTE, $y);
    }

    /**
     * Creates an IS NULL expression with the given arguments.
     *
     * @param string $x The field in string format to be restricted by IS NULL.
     *
     * @return string
     */
    public function isNull($x)
    {
        return $x . ' IS NULL';
    }

    /**
     * Creates an IS NOT NULL expression with the given arguments.
     *
     * @param string $x The field in string format to be restricted by IS NOT NULL.
     *
     * @return string
     */
    public function isNotNull($x)
    {
        return $x . ' IS NOT NULL';
    }

    /**
     * Creates a LIKE() comparison expression with the given arguments.
     *
     * @param string $x Field in string format to be inspected by LIKE() comparison.
     * @param mixed  $y Argument to be used in LIKE() comparison.
     *
     * @return string
     */
    public function like($x, $y)
    {
        return $this->comparison($x, 'LIKE', $y);
    }

    /**
     * Creates a NOT LIKE() comparison expression with the given arguments.
     *
     * @param string $x Field in string format to be inspected by NOT LIKE() comparison.
     * @param mixed  $y Argument to be used in NOT LIKE() comparison.
     *
     * @return string
     */
    public function notLike($x, $y)
    {
        return $this->comparison($x, 'NOT LIKE', $y);
    }

    /**
     * Creates a IN () comparison expression with the given arguments.
     *
     * @param string       $x The field in string format to be inspected by IN() comparison.
     * @param string|array $y The placeholder or the array of values to be used by IN() comparison.
     *
     * @return string
     */
    public function in($x, $y)
    {
        return $this->comparison($x, 'IN', '('.implode(', ', (array) $y).')');
    }

    /**
     * Creates a NOT IN () comparison expression with the given arguments.
     *
     * @param string       $x The field in string format to be inspected by NOT IN() comparison.
     * @param string|array $y The placeholder or the array of values to be used by NOT IN() comparison.
     *
     * @return string
     */
    public function notIn($x, $y)
    {
        return $this->comparison($x, 'NOT IN', '('.implode(', ', (array) $y).')');
    }

    /**
     * Quotes a given input parameter.
     * @param $input
     * @return string
     */
    public function literal($input)
    {
        return $this->DBInstance->quote($input);
    }
    /**
     * Creates a BETWEEN comparison expression with the given arguments.
     * Example:
     *
     *     [php]
     *     $qb->where($qb->expr()->between('u.id', '1', '10'));
     * @param $input
     * @param $x
     * @param $y
     * @return string
     */
    public function between($input, $x, $y){
        if(!is_numeric($x) or !is_numeric($y))
            return false;
        if($x>$y){$tmp=$x; $x=$y; $y=$tmp;}
        return $this->comparison($input, 'BETWEEN', $x . ' AND '.$y);
    }
    /**
     * Creates a EXISTS() expression with the given arguments.
     * Example:
     *
     *     [php]
     *     $qb->where($qb->expr()->exists($qb2->getQuery()));
     * @param $subquery
     * @return string
     */
    public function exists($subquery){
        return 'EXISTS ('.$subquery.')';

    } // Returns Expr\Func instance
    /**
     * Creates a COUNT() expression with the given arguments.
     * Example:
     *
     *     [php]
     *     $qb->select($qb->expr()->count('u.surname'));
     * @param $x
     * @return string
     */
    public function count($x){
        return 'COUNT('.implode(', ', func_get_args()).')';
    }
    /**
     * Creates a COUNT(DISTINCT val) expression with the given arguments.
     * Example:
     *
     *     [php]
     *     $qb->select($qb->expr()->countDistinct('u.surname'));
     * @param $x
     * @return string
     */
    public function countDistinct($x){
        return 'COUNT(DISTINCT ' . implode(', ', func_get_args()) . ')';
    }

    /**
     * Creates an  AVG() expression , with the given argument..
     * Example:
     *
     *     [php]
     *     $qb->select($qb->expr()->avg('u.age'));
     * @param mixed $x Argument to be used in AVG() function.
     * @return string
     */
    public function avg($x){
        return 'AVG('. implode(', ', func_get_args()).')';
    }
    /**
     * Creates a MAX() expression , with the given argument..
     * Example:
     *
     *     [php]
     *     $qb->select($qb->expr()->max('u.age'));
     * @param mixed $x Argument to be used in Max() function.
     * @return string
     */
    public function max($x){
        return 'MAX('. implode(', ', func_get_args()).')';
    }
    /**
     * Creates a MIN() expression , with the given argument..
     * Example:
     *
     *     [php]
     *     $qb->select($qb->expr()->min('u.age'));
     * @param mixed $x Argument to be used in MIN() function.
     * @return string
     */
    public function min($x){
        return 'MIN('. implode(', ', func_get_args()).')';
    }
    /**
     * Creates a CONCAT() expression with the given arguments.
     * Example:
     *
     *     [php]
     *     $qb->select($qb->expr()->concat('u.firstname', $qb->expr()->concat($qb->expr()->literal(' '), 'u.lastname')));
     *
     * @param $x
     * @param $y
     * @return string
     */
    public function concat($x,$y){
        return 'CONCAT('. implode(', ', func_get_args()).')';
    }
    /**
     * Creates a SUBSTRING() function expression with the given arguments.
     *
     * @param mixed    $x    Argument to be used as string to be cropped by SUBSTRING() function.
     * @param int      $from Initial offset to start cropping string. May accept negative values.
     * @param int|null $len  Length of crop. May accept negative values.
     *
     * @return string
     */
    public function substring($x, $from, $len = null)
    {
        $args = array($x, $from);
        if (null !== $len) {
            $args[] = $len;
        }
        return 'SUBSTRING('. implode(', ', $args).')';
    }
    /**
     * Creates a LPAD() function expression with the given arguments.
     *
     * @param mixed    $x    Argument to be used as string to be cropped by LPAD() function.
     * @param mixed      $prefix Initial offset to start cropping string. May accept negative values.
     * @param mixed|null $suffix  Length of crop. May accept negative values.
     *
     * @return string
     */
    public function lpad($x, $prefix, $suffix = null)
    {
        $args = array($x, $prefix);
        if (null !== $suffix) {
            $args[] = $suffix;
        }
        return 'LPAD('. implode(', ', $args).')';
    }
    /**
     * @param $leftExpr
     * @param $operator
     * @param $rightExpr
     * @return string
     */
    private function math($leftExpr,$operator,$rightExpr)
    {
        $leftExpr = (string) $leftExpr;
        $rightExpr = (string) $rightExpr;
        return $leftExpr . ' ' . $operator . ' ' . $rightExpr;
    }

    /**
     * Creates a product mathematical expression with the given arguments.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> * <right expr>. Example:
     *
     *     [php]
     *     // u.salary * u.percentAnnualSalaryIncrease
     *     $q->expr()->prod('u.salary', 'u.percentAnnualSalaryIncrease')
     *
     * @param mixed $x Left expression.
     * @param mixed $y Right expression.
     * @return string
     */
    public function prod($x, $y)
    {
        return $this->math($x, '*', $y);
    }
    /**
     * Creates a difference mathematical expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> - <right expr>. Example:
     *
     *     [php]
     *     // u.monthlySubscriptionCount - 1
     *     $q->expr()->diff('u.monthlySubscriptionCount', '1')
     *
     * @param mixed $x Left expression.
     * @param mixed $y Right expression.
     *
     * @return string
     */
    public function diff($x, $y)
    {
        return $this->math($x, '-', $y);
    }
    /**
     * Creates a sum mathematical expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> + <right expr>. Example:
     *
     *     [php]
     *     // u.numChildren + 1
     *     $q->expr()->sum('u.numChildren', '1')
     *
     * @param mixed $x Left expression.
     * @param mixed $y Right expression.
     *
     * @return string
     */
    public function sum($x, $y)
    {
        return $this->math($x, '+', $y);
    }
    /**
     * Creates a quotient mathematical expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> / <right expr>. Example:
     *
     *     [php]
     *     // u.total / u.period
     *     $expr->quot('u.total', 'u.period')
     *
     * @param mixed $x Left expression.
     * @param mixed $y Right expression.
     *
     * @return string
     */
    public function quot($x, $y)
    {
        return $this->math($x, '/', $y);
    }

}
