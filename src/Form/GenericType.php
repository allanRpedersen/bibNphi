<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;

class GenericType extends AbstractType
{

		/**
	 * Crée un configuration de base pour les champs du formulaire
	 *
	 * @param string $label
	 * @param string $placeholder
	 * @param boolean $required
	 * @return array
	 */
	protected function mkBasics( $label, $placeholder, $required=true, $options=[] )
	{
		return array_merge([
			'label' => $label,
			'attr' => [
				'placeholder'=> $placeholder
			],
			'required' => $required,
		], $options);
	}


}