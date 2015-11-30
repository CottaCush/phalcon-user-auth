<?php

namespace UserAuth\Models;

use \Phalcon\Di;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use UserAuth\Exceptions\UserTypeException;
use UserAuth\Libraries\Utils;

/**
 * Class UserLoginHistory
 * @property int id
 * @property string name
 * @property string created_at
 * @property string updated_at
 * @package UserAuth\Models
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class UserType extends BaseModel
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return "user_types";
    }

    /**
     * Set field before validation check
     */
    public function beforeValidationOnCreate()
    {
        $this->created_at = Utils::getCurrentDateTime();
    }

    public function beforeValidationOnUpdate()
    {
        $this->updated_at = Utils::getCurrentDateTime();
    }

    /**
     * Validate user type entered
     * @return bool
     */
    public function validation()
    {
        $this->validate(new PresenceOf([
            'field' => 'name',
            'message' => 'User type name must be supplied'
        ]));

        $this->validate(new UniquenessValidator(array(
            'field' => 'name',
            'message' => 'Sorry, the user type already exists'
        )));


        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    /**
     * Create  user type/role
     * @param string $name
     * @return int
     * @throws UserTypeException
     */
    public function createUserType($name)
    {
        $userType = new self();
        $userType->name = $name;
        if (!$userType->create()) {
            throw new UserTypeException($this->getMessages());
        }
        return $userType->id;
    }

    /**
     * Return all user types/roles
     * @return $this[]
     */
    public function getUserTypes()
    {
        return $this->find();
    }
}