<?php

namespace Models\Offers;

use Models\Model;

class OfferPhotos extends Model
{
    protected $photo_cian_id;
    protected $offer_id;

    public function __get($name)
    {
        switch ($name) {
            case 'id':
            case 'offer_id':
            case 'photo_cian_id':
                return $this->{$name};
            default:
                return null;
        }
    }

    protected static function getTableName(): string
    {
        return 'offers_photos';
    }
}