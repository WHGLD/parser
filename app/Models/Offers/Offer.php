<?php

namespace Models\Offers;

use Models\CianUsers\CianUser;
use Models\Model;
use Models\Phones\Phones;
use Services\Db;

class Offer extends Model
{
    protected $cian_id;
    protected $url;
    protected $address;
    protected $floor;
    protected $price;
    protected $client_fee;
    protected $agent_fee;
    protected $deposit;
    protected $is_imported;
    protected $is_new;
    protected $cian_user_id;

    protected $updated_date;
    protected $created_date;
    protected $parsed_date;

    protected $cian_user;
    protected $photos;
    protected $phones;

    public function __get($name)
    {
        switch ($name) {
            case 'id':
            case 'cian_id':
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
            case 'cian_user_id':
                return $this->{$name};
            case 'cian_user':
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

    public function getCianUser(): ?CianUser
    {
        return CianUser::getById($this->cian_user_id);
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
        if (isset($mappedProperties['cian_user']) && $mappedProperties['cian_user'] instanceof CianUser){
            $cianUser = $mappedProperties['cian_user']->save();
            $mappedProperties['cian_user_id'] = $cianUser->id;
            unset($mappedProperties['cian_user']);
        }

        parent::insert($mappedProperties); // @todo не сохранять полные копии
    }
}