<?php
namespace Backend\Base\Models;
class Value extends \Backend\Base\Model
{
	protected $id;
	protected $name;
	protected $value;

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getValue()
	{
		return $this->value;
	}
}