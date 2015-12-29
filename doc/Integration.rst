Integration
===========

Doctrine integration
--------------------

Independently from your preferred framework's characteristics, it is also possible to use plain
Doctrine with the Money VO, using its Embeddables_ feature.

.. _Embeddables: http://doctrine-orm.readthedocs.org/en/latest/tutorials/embeddables.html

Since writing annotations on the third party's library files is not applicable, you'll want to use
either the `YAML mapping`_ or `XML mapping`_.

.. _YAML mapping: http://doctrine-orm.readthedocs.org/en/latest/reference/yaml-mapping.html#example
.. _XML mapping: http://doctrine-orm.readthedocs.org/en/latest/reference/xml-mapping.html#example

The following two yaml files for Money and Currency turned out working well:

.. code-block:: yaml

    'Money\Money':
      type: embeddable
      table: 'money' # the persistence table name you'd like to use

      embedded:
        'currency':
          class: 'Money\Currency'

      fields:
        'amount':
          type: integer
          nullable: false
          options:
            unsigned: true

.. code-block:: yaml

    'Money\Currency':
      type: embeddable
      table: 'money_currency' # the persistence table name you'd like to use

      fields:
        'code':
          type: string
          nullable: false
          length: 3

Symfony configuration
^^^^^^^^^^^^^^^^^^^^^

For convenience, see the following configuration which would work out of the box for your
Symfony2 stack:

.. code-block:: yaml

    doctrine:
        orm:
            entity_managers:
                default: #or whatever fits in your case
                    mappings:
                        'Money':
                            type: yml
                            # find the above mentioned files Money.orm.yml and Currency.orm.yml
                            # in app/config/persistence/Money/ :
                            dir: '%kernel.root_dir%/config/persistence/Money'
                            # The PHP namespace prefix:
                            prefix: 'Money'
                            is_bundle: false
