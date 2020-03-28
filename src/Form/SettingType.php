<?php

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('keywords')
            ->add('email')
            ->add('telno')
            ->add('facebook')
            ->add('twitter')
            ->add('instagram')
            ->add('smtpserver')
            ->add('smtpmail')
            ->add('smtppassword')
            ->add('smtpport')
            ->add('aboutus')
            ->add('contanctus')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}
