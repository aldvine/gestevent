<?php
// src/AppBundle/Form/ProfileType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', null, array('label' => 'form.firstname', 'translation_domain' => 'FOSUserBundle'));
        $builder->add('lastname', null, array('label' => 'form.lastname', 'translation_domain' => 'FOSUserBundle'));
    
    }  
   
    public function getParent()
    {
        return  'FOS\UserBundle\Form\Type\ProfileFormType';
    }
    public function getBlockPrefix()
    {
        return 'app_user_profile_edit';
    }
    public function getName()
    {
        return 'app_user_profile_edit';
    }
}
?>
