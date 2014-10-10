<?php


namespace Ogi\DateIntervalBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateIntervalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('years', 'choice', array(
                'choices' => range(0, $options['nb_years_choices'], 1)
            ))
            ->add('months', 'choice', array(
                'choices' => range(0, 11, 1)
            ))
            ->add('days', 'choice', array(
                'choices' => range(0, 30, 1)
            ));

        if ($options['with_hours']) {
            $builder->add('hours', 'choice', array(
                'choices' => range(0, 23, 1)
            ));
        }

        if ($options['with_minutes']) {
            $builder->add('minutes', 'choice', array(
                'choices' => range(0, 59, 1)
            ));
        }

        if ($options['with_seconds']) {
            $builder->add('seconds', 'choice', array(
                'choices' => range(0, 59, 1)
            ));
        }

        //Transform to \DateInterval & reverse
        $builder->addViewTransformer(new CallbackTransformer(function (\DateInterval $value = null) {

            if($value === null) return null;

            return array(
                'seconds' => $value->s,
                'minutes' => $value->i,
                'hours' => $value->h,
                'days' => $value->d,
                'months' => $value->m,
                'years' => $value->y,
            );

        }, function ($value) {

            if($value === null) return null;

            $interval = new \DateInterval('PT0S');

            $interval->s = isset($value['seconds']) ? $value['seconds'] : 0;
            $interval->i = isset($value['minutes']) ? $value['minutes'] : 0;
            $interval->h = isset($value['hours']) ? $value['hours'] : 0;
            $interval->d = isset($value['days']) ? $value['days'] : 0;
            $interval->m = isset($value['months']) ? $value['months'] : 0;
            $interval->y = isset($value['years']) ? $value['years'] : 0;

            //If no interval, return null
            $now = new \DateTime();
            $copy = clone $now;
            return $now->getTimestamp() === $copy->add($interval)->getTimestamp()
                ? null
                : $interval;
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'with_hours' => true,
            'with_minutes' => true,
            'with_seconds' => false,
            'data_class' => null,
            'compound' => true,
            'nb_years_choices' => 5, //Number of default years choices
        ));

        $resolver->setOptional(array(
            'empty_value',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ogi_dateinterval';
    }

}
