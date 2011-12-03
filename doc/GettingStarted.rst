
Getting started
===============

All amounts are represented in the smallest unit (eg. cents), so USD 5.00 is written as

.. code-block:: php
   
   <?php
   $fiver = new Money(500, new Currency('USD'));
   // or shorter:
   $fiver = Money::USD(500);

Autoloading
-----------

You'll need an autoloader. Money is PSR-0 compatible, so if you are using the Symfony2 autoloader, this will do:

.. code-block:: php
   
   <?php
   use Symfony\Component\ClassLoader\UniversalClassLoader;
   
   $loader = new UniversalClassLoader;
   $loader->registerNamespaces(array(
      'Money' => __DIR__ . '/vendor/money/lib/',
   ));
   $loader->register();
      
