<?php

namespace UtilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JqueryAutocompleteType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {

	}

	public function buildView( FormView $view, FormInterface $form, array $options ) {
		$sPropertyValue = '';

		$oEntity = $form->getData();

		if ( ! is_null( $form->getData() ) ) {

			if ( isset( $options['property'] ) ) {

				$getProperty = 'get' . ucwords( $options['property'] );

				$sPropertyValue = $oEntity->$getProperty();
			} else {
				$sPropertyValue = $oEntity->__toString();
			}
		}

		$view->vars = array_replace( $view->vars,
			array(
				'class'         => $options['class'],
				'property'      => $options['property'],
				'suggest_value' => $sPropertyValue,
				'search_method' => $options['search_method'],
				'route_name'    => $options['route_name'],
				'tpl'           => $options['tpl'],
				'extraParams'   => $options['extraParams']
			) );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'query_builder'  => null,
			'class'          => null,
			'slug_parametro' => null,
			'search_method'  => 'autocompleteSearch',
			'related_method' => null,
			'route_name'     => 'ajax_default',
			'tpl'            => null,
			'extraParams'    => null
		) );
	}

	public function getParent() {
		return 'entity';
	}

	public function getBlockPrefix() {
		return 'util_bundle_jquery_autocomplete_type';
	}
}
