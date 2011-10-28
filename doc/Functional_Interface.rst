Functional Interface
====================

If you include the lib/Money/Functions.php file, you'll get a couple of shortcuts 
for creating Money instances. You can of course define your own shortcut functions if the 
currencies you need are not available on your keyboard layout.

.. code:: php
	
	<?php
	require_once 'lib/Money/Functions.php';
	
	$fiveEur   = €(500);
	$fiveUsd   = §(500); // $ is already taken in PHP
	$fiveYen   = ¥(500);
	$fivePound = £(500);

This is legal PHP, but some IDE's may disagree and mark the functions as syntax errors.
Report a bug to your IDE vendor if that's the case.

As these functions are just wrappers for ``new Money($amount, $currency)``, all the 
same methods are available:

	<?php
	assert(
		€(100)->equals(
			€(400)->add(
				€(600)
			)
	); 