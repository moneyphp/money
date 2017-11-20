<?php
namespace Money\Traits;

trait OperatorOverloads
{
    // Overloads + operator
    public function __add($__value__)
    {
        return $this->add($__value__);
    }

    // Overloads - operator
    public function __sub($__value__)
    {
        return $this->subtract($__value__);
    }

    // Overloads * operator
    public function __mul($__value__)
    {
        return $this->multiply($__value__);
    }

    // Overloads / operator
    public function __div($__value__)
    {
        return $this->divide($__value__);
    }

    // Overloads ** operator
    public function __pow($__value__)
    {
        throw new \Exception("Function not implemented");
    }

    // Overloads . operator
    public function __concat($__value__)
    {
        throw new \Exception("Function not implemented");
    }

    // Overloads % operator
    public function __mod($__value__)
    {
        return $this->mod($__value__);
    }

    // Overloads << operator
    public function __sl($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads >> operator
    public function __sr($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads | operator
    public function __or($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads &
    public function __and($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads ^
    public function __xor($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads ===
    public function __is_identical($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads !==
    public function __is_not_identical($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads ==
    public function __is_equal($__value__)
    {
        return $this->equals($__value__);
    }

    // Overloads !=
    public function __is_not_equal($__value__)
    {
        return !$this->equals($__value__);
    }
    // Overloads <
    public function __is_lesser($__value__)
    {
        return $this->lessThan($__value__);
    }

    // Overloads <=
    public function __is_lesser_or_equal($__value__)
    {
        return $this->lessThanOrEqual($__value__);
    }

    // Overloads >
    public function __is_greater($__value__)
    {
        return !$this->lessThan($__value__);
    }

    // Overloads >=
    public function __is_greater_or_equal($__value__)
    {
        return !$this->lessThan($__value__) || $this->equals($__value__);
    }

    // Overloads +=
    public function __assign_add($__value__)
    {
        return $this->add($__value__);
    }

    // Overloads -=
    public function __assign_sub($__value__)
    {
        return $this->subtract($__value__);
    }

    // Overloads *=
    public function __assign_mul($__value__)
    {
        return $this->multiply($__value__);
    }

    // Overloads /=
    public function __assign_div($__value__)
    {
        return $this->divide($__value__);
    }

    // Overloads %=
    public function __assign_mod($__value__)
    {
        return $this->mod($__value__);
    }

    // Overloads <=
    public function __assign_sl($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads >>=
    public function __assign_sr($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads .=
    public function __assign_concat($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads |=
    public function __assign_or($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads &=
    public function __assign_and($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads ^=
    public function __assign_xor($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads **=
    public function __assign_pos($__value__){
        throw new \Exception("Function not implemented");
    }

    // Overloads ++$value
    public function __pre_inc(){
        throw new \Exception("Function not implemented");
    }

    // Overloads --$value
    public function __pre_dec(){
        throw new \Exception("Function not implemented");
    }

    // Overloads $value++
    public function __post_inc(){
        throw new \Exception("Function not implemented");
    }

    // Overloads $value--
    public function __post_dec(){
        throw new \Exception("Function not implemented");
    }
}