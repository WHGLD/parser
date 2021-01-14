<?php

namespace Models\Offers;

use Models\Users\User;
use Models\Model;
use Models\Phones\Phones;
use Services\Db;

class Offer extends Model
{
    CONST EXTERNAL_TYPE_DEFAULT = 0;
    CONST EXTERNAL_TYPE_CIAN = 1;

    protected $external_id;
    protected $external_type;
    protected $url;
    protected $address;
    protected $floor;
    protected $price;
    protected $client_fee;
    protected $agent_fee;
    protected $deposit;
    protected $is_imported;
    protected $is_new;
    protected $user_id;

    protected $updated_date;
    protected $created_date;
    protected $parsed_date;

    protected $user;
    protected $photos;
    protected $phones;

    public function __get($name)
    {
        switch ($name) {
            case 'id':
            case 'external_id':
            case 'external_type':
            case 'url':
            case 'address':
            case 'floor':
            case 'price':
            case 'client_fee':
            case 'agent_fee':
            case 'deposit':
            case 'updated_date':
            case 'created_date':
            case 'parsed_date':
            case 'is_imported':
            case 'is_new':
            case 'user_id':
                return $this->{$name};
            case 'user':
                return $this->getCianUser();
//            case 'photos':
//                return $this->getPhotos();
//            case 'phones':
//                return $this->getPhones();
            default:
                return null;
        }
    }

    protected static function getTableName(): string
    {
        return 'offers';
    }

    public function getCianUser(): ?User
    {
        return User::getById($this->user_id);
    }

//    public function getPhotos(): ?array
//    {
//        return Db::getInstance()->query(
//            'SELECT * FROM `' . OfferPhotos::getTableName() . '` WHERE offer_id=:id;',
//            [':id' => $this->id],
//            OfferPhotos::class
//        );
//    }

//    public function getPhones(): ?array
//    {
//        return Db::getInstance()->query(
//            'SELECT * FROM `' . Phones::getTableName() . '` WHERE offer_id=:id;',
//            [':id' => $this->id],
//            Phones::class
//        );
//    }

    /**
     * @param array $mappedProperties
     * @return void
     */
    protected function insert(array $mappedProperties): void
    {
        if (isset($mappedProperties['user']) && $mappedProperties['user'] instanceof User){
            $cianUser = $mappedProperties['user']->save();
            $mappedProperties['user_id'] = $cianUser->id;
            unset($mappedProperties['user']);
        }

        parent::insert($mappedProperties); // @todo не сохранять полные копии
    }
}