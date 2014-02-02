<?php
namespace York\Validator;
use York\Exception\Validator;

/**
 * validator for checking that the given data has a minimum length
 *
 * @author wolxXx
 * @version 3.0
 * @package York\Validator
 */
class HasMinimumLength implements ValidatorInterface{
	/**
	 * @var integer
	 */
	protected $minimumLength;

	/**
	 * @param integer $minimumLength
	 */
	public function __construct($minimumLength){
		$this->minimumLength = $minimumLength;
	}

	/**
	 * @param mixed $data
	 * @return null
	 * @throws \York\Exception\Validator
	 */
	public function isValid($data){
		if(strlen($data) < $this->minimumLength){
			throw new Validator('given string has not the minimum length');
		}

		return true;
	}
}