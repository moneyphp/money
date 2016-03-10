Json
====

If you want to serialize a money object into a JSON, you can just use the PHP method ``json_encode`` for that.
Please find below example of how to achieve this.

.. code-block:: php

    use Moeny\Money;

    $money = Money::USD(350);
    $json = json_encode($money);
    echo $json; // outputs '{"amount":"350","currency":"USD"}'
