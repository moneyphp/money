<?php

declare(strict_types=1);

namespace spec\Money\Calculator;

use Money\Calculator;

use function rand;

/**
 * Mocking with typehints usage won't work here as the trait is autoloaded.
 *
 * @see https://github.com/phpspec/phpspec/issues/825
 */
trait CalculatorBehavior
{
    public function it_is_a_calculator(): void
    {
        $this->shouldImplement(Calculator::class);
    }

    public function it_compares_two_values(): void
    {
        $this->compare(2, 1)->shouldReturn(1);
        $this->compare(1, 2)->shouldReturn(-1);
        $this->compare(1, 1)->shouldReturn(0);
    }

    public function it_adds_two_values(): void
    {
        $this->add(rand(-100, 100), rand(-100, 100))->shouldBeString();
    }

    public function it_subtracts_a_value_from_another(): void
    {
        $this->subtract(rand(-100, 100), rand(-100, 100))->shouldBeString();
    }

    public function it_multiplies_a_value_by_another(): void
    {
        $this->multiply(rand(-100, 100), rand(-100, 100))->shouldBeString();
    }

    public function it_divides_a_value_by_another(): void
    {
        $this->divide(rand(-100, 100), rand(1, 100))->shouldBeString();
    }

    public function it_ceils_a_value(): void
    {
        $this->ceil(rand(-100, 100) / 100)->shouldBeString();
    }

    public function it_floors_a_value(): void
    {
        $this->floor(rand(-100, 100) / 100)->shouldBeString();
    }

    public function it_calculates_the_absolute_value(): void
    {
        $result = $this->absolute(rand(1, 100));

        $result->shouldBeGreaterThanZero();
        $result->shouldBeString();

        $result = $this->absolute(rand(-100, -1));

        $result->shouldBeGreaterThanZero();
        $result->shouldBeString();
    }

    public function it_shares_a_value(): void
    {
        $this->share('10', '2', '4')->shouldBeString();
    }

    public function it_calculates_the_modulus(): void
    {
        $this->mod('11', '5')->shouldBeString();
    }

    /** {@inheritDoc} */
    public function getMatchers(): array
    {
        return [
            'beGreaterThanZero' => static function ($subject) {
                return $subject > 0;
            },
        ];
    }
}
