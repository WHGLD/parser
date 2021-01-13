<?php

namespace Models\CianUsers;

use Models\Model;
use Models\Phones\Phones;

class CianUser extends Model
{
    protected $cian_id;
    protected $published_id;
    protected $agency_name;
    protected $company_name;
    protected $is_agent;

    protected $phones;

    public function __get($name)
    {
        switch ($name) {
            case 'id':
            case 'cian_id':
            case 'published_id':
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
        return 'cian_users';
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
        $isUserExists = self::getByField('cian_id', $mappedProperties['cian_id']);

        if (!$isUserExists){
            parent::insert($mappedProperties);
        }
    }
}