<?php

namespace Euro\CoinBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ValueType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('value')
            ->add('collector')
        ;
    }

    public function getName()
    {
        return 'euro_coinbundle_valuetype';
    }
}
