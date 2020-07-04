<?php

namespace App\Form\Filter;

use App\Entity\Dependencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Type\JqueryAutocompleteType;

class ApiExpedienteFilterType extends ExpedienteFilterType {
    /**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return '';
	}


}
