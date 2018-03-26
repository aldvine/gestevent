<?php
// src/AppBundle/Form/RegistrationType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('firstname', null, array('label' => 'form.firstname', 'translation_domain' => 'FOSUserBundle'));
        $builder->add('lastname', null, array('label' => 'form.lastname', 'translation_domain' => 'FOSUserBundle'));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
    public function getName()
    {
        return 'app_user_registration';
    }
}
?>
