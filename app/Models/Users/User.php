<?php

namespace Models\Users;

use Models\Model;
use Models\Phones\Phones;

class User extends Model
{
    protected $external_id;
    protected $agency_name;
    protected $company_name;
    protected $is_agent;

    protected $phones;

    public function __get($name)
    {
        switch ($name) {
            case 'id':
            case 'external_id':
            case 'agency_name':
            case 'company_name':
            case 'is_agent':
                return $this->{$name};
//            case 'phones':
//                return $this->getPhones();
            default:
                return null;
        }
    }

    protected static function getTableName(): string
    {
        return 'users';
    }

//    public function getPhones()
//    {
//    }

    /**
     * @param array $mappedProperties
     * @return void
     */
    protected function insert(array $mappedProperties): void
    {
        $isUserExists = self::getByField('external_id', $mappedProperties['external_id']);

        if (!$isUserExists){
            parent::insert($mappedProperties);
        }
    }
}