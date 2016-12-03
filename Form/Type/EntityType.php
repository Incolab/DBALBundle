<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Incolab\DBALBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Incolab\DBALBundle\Service\DBALService;

/**
 * Description of ForumRoleEntityType
 *
 * @author david
 */
class EntityType extends AbstractType {

    protected $dbalService;

    public function __construct(DBALService $dbalService) {
        $this->dbalService = $dbalService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $choiceLoader = function (Options $options) {
            $data = $options["query"]($this->dbalService);
            return $data;
        };


        $resolver->setDefaults(array(
            'choices' => $choiceLoader,
        ));
        $resolver->setRequired(array('class', 'query'));
    }

    public function getParent() {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

}
