<?php
namespace York\HTML\Element;
/**
 * a dropdown group element
 *
 * @author wolxXx
 * @version 3.0
 * @package \York\HTML\Element
 */
class DropdownGroup extends \York\HTML\ContainableDomElementAbstract{
	/**
	 * @param array $data
	 * @return \York\HTML\Element\DropdownGroup
	 */
	public static function Factory($data = array()){
		return parent::Factory($data);
	}

	/**
	 * (non-PHPdoc)
	 * @see DomElementInterface::getDefaultConf()
	 */
	public static function getDefaultConf(){
		return array(
			'label' => null
		);
	}

	/**
	 * overwrites the abstract method
	 * it only accepts dropdown elements
	 *
	 * @param \York\HTML\DomElementInterface $child
	 * @throws \York\Exception\HTTMLGenerator
	 * @return \York\HTML\Element\DropdownGroup
	 */
	public function addChild(\York\HTML\DomElementInterface $child){
		if($child instanceof \York\HTML\Element\DropdownElement){
			parent::addChild($child);
			return $this;
		}
		throw new \York\Exception\HTTMLGenerator('dropdown group container can only contain dropdown elements as children');
	}

	/**
	 * overwrites the abstract method
	 * because label is something different in this context
	 * as <optgroup label="foo"> is not <label>foo</label> <optgroup>
	 *
	 * @param string $text
	 * @param string $position
	 * @return \York\HTML\Element\DropdownGroup
	 */
	public function addLabel($text = null, $position = 'before'){
		$this->set('label', $text);
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see DomElementInterface::display()
	 */
	public function display(){
		$conf = $this->getConf();
		\York\HTML\Core::out(
			\York\HTML\Core::openTag('optgroup', $conf)
		);

		/**
		 * @var \York\HTML\DomElementAbstract $current
		 */
		foreach($this->children as $current){
			$current->display();
		}
		\York\HTML\Core::out(
			\York\HTML\Core::closeTag('optgroup')
		);
		return $this;
	}
}