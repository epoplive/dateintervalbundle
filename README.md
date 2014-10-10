#DateInterval Bundle

##Description

Provides additonnal features to handle DateIntervals (A period of time, ex: 2 months and 4 days).

This bundle provides:

- A DBAL Type to save \DateInterval to database. (Based on [herrera-io/php-doctrine-dateinterval](https://github.com/herrera-io/php-doctrine-dateinterval), but let you handle the DBAL Type registration)
- A FormType, providing a way to get user input.

The ["herrera-io/date-interval"](https://github.com/herrera-io/php-date-interval) dependency also add the following features:

- convert `DateInterval` to the [interval spec](http://php.net/manual/en/dateinterval.construct.php)
- convert `DateInterval` to the number of seconds
    - convert back to `DateInterval` from the number of seconds

> The conversion to seconds requires [a bit of explaining](https://github.com/herrera-io/php-date-interval/wiki/API#wiki-toSeconds).

##Installation

Add the following dependency to your composer.json file:

``` json
{
    "require": {
        "_other_packages": "...",
        "ogizanagi/dateintervalbundle": "dev-master"
    }
}

```

##Use the new DBAL type

### Register new DBAL type

config.yml:

``` yaml
doctrine:
    dbal:
        types:
            dateinterval: Ogi\DateIntervalBundle\DBAL\Types\DateIntervalType

        connections:
            default:
                driver:   %database_driver%
                ...

                #Custom type declarations for SchemaTool
                mapping_types:
                    dateinterval: bigint
```

### Use the DBAL Type

```php
<?php

/**
 * @Entity()
 * @Table(name="Reminders")
 */
class Reminder
{
    /**
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     * @Id()
     */
    private $id;

    /**
     * @Column(type="dateinterval")
     */
    private $interval;

    /**
     * @return DateInterval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param DateInterval $interval
     */
    public function setInterval(DateInterval $interval = null)
    {
        $this->interval = $interval;
    }
}

//Remind me every day
$reminder = new Reminder();
$reminder->setInterval(new DateInterval('P1D'));

$em->persist($annualJob);
$em->flush();

echo $reminder->getInterval()->toSpec(); // "P1D"
```

> **NOTICE** The date interval instances returned are of `Herrera\DateInterval\DateInterval`, inherited from [\DateInterval](http://php.net/manual/fr/class.dateinterval.php)


##Use the DateInterval FormType

``` php

public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ...

        $builder
            ->add('interval', 'ogi_dateinterval', array(
                'label' => 'Remind me every...',
                'required' => false,
                'with_hours' => false,
                'with_minutes' => false,
                'with_seconds' => false,
            ))
        ;
    }
```
