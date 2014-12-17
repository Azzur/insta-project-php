<?php

namespace Insta\PlanningBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OralType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('course', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Course' ,
                'property'=>'name',
                'label' => 'schedule.course'

            ))
            ->add('students', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Student',
                'property' => 'fullname',
                'multiple'=> true,
                'label' => 'schedule.students'
            ))
            ->add('room', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Room' ,
                'property'=>'name',
                'label' => 'schedule.room'
            ))
            ->add('datetime', 'datetime', array('label'=>'schedule.datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd H:m',
                'attr' => array('class' => 'input-append date form_datetime')))
            ->add('duration', 'datetime', array('label'=>'schedule.duration',
                'widget' => 'single_text',
                'format' => 'H:m',
                'attr' => array('class' => 'input-append bootstrap-timepicker')))
            ->add('add', 'submit', array('label' => 'form.add'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Insta\PlanningBundle\Entity\Oral'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'insta_planningbundle_oral';
    }
}
