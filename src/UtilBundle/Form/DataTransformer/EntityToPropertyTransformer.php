<?php

//namespace Tetranz\Select2EntityBundle\Form\DataTransformer;
namespace UtilBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Data transformer for single mode (i.e., multiple = false)
 *
 * Class EntityToPropertyTransformer
 * @package Tetranz\Select2EntityBundle\Form\DataTransformer
 */
class EntityToPropertyTransformer implements DataTransformerInterface {
	private $manager;
	protected $className;
	protected $textProperty;

	public function __construct( ObjectManager $manager, $class, $textProperty ) {
		$this->manager      = $manager;
		$this->className    = $class;
		$this->textProperty = $textProperty;
	}

	/**
	 * Transform entity to json with id and text
	 *
	 * @param mixed $entity
	 *
	 * @return string
	 */
	public function transform( $entity ) {

//    	$entity = $this->className;
		if ( null === $entity || '' === $entity ) {
			return '';
		}

		$accessor = PropertyAccess::createPropertyAccessor();

		// return the initial values as html encoded json

		$text = is_null( $this->textProperty )
			? (string) $entity
			: $accessor->getValue( $entity, $this->textProperty );

		$data = array(
			'id'   => $accessor->getValue( $entity, 'id' ),
			'text' => $text
		);

//        return htmlspecialchars(json_encode($data));
		return ( json_encode( $data ) );
	}

	/**
	 * Transform to single id value to an entity
	 *
	 * @param string $value
	 *
	 * @return mixed|null|object
	 */
	public function reverseTransform( $value ) {
		if ( null === $value || '' === $value ) {
			return null;
		}

		$repo = $this->manager->getRepository( $this->className );

		return $repo->find( $value );
	}

}
