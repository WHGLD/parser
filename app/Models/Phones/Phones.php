<?php

namespace Models\Phones;

use Models\Model;

class Phones extends Model
{
    protected $number;
    protected $offer_id;
    protected $cian_user_id;

    public function __get($name)
    {
        switch ($name) {
            case 'id':
            case 'number':
            case 'offer_id':
            case 'cian_user_id':
                return $this->{$name};
            default:
                return null;
        }
    }

    protected static function getTableName(): string
    {
        return 'phones';
    }

    /**
     * @param array $mappedProperties
     * @return void
     */
    protected function insert(array $mappedProperties): void
    {
        $isNumberExists = self::getByField('number', $mappedProperties['number']);

        if (!$isNumberExists){
            parent::insert($mappedProperties);
        }
    }
}