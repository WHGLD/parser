<?php

namespace Services\Cian;

use Models\Users\User;
use Models\Offers\Offer;
use Services\DataHandlerInterface;

class CianDataHandler implements DataHandlerInterface
{
    protected $offersArr = [];

    /**
     * @param $data
     * @return array
     */
    public function process(array $data): array
    {
        foreach ($data as $dataItem){
            $cianUser = User::create([
                'external_id' => (int)$dataItem->cianUserId, // id пользака на сайте cian
                'agency_name' => isset($dataItem->user->agencyName) ? (string)$dataItem->user->agencyName : null,
                'company_name' => isset($dataItem->user->companyName) ? (string)$dataItem->user->companyName : null,
                'is_agent' => (int)$dataItem->user->isAgent,
                // 'phones' => isset($data->user->phoneNumbers) ? $this->phoneHandler($data->user->phoneNumbers) : [], // @todo
            ]);

            $this->offersArr[] = Offer::create([
                # общие данные
                'external_id' => (int)$dataItem->cianId,
                'external_type' => Offer::EXTERNAL_TYPE_CIAN,
                'url' => (string)$dataItem->fullUrl,
                'phones' => isset($dataItem->phones) ? $this->phoneHandler($dataItem->phones) : [],
                'address' => (string)$this->addressHandler($dataItem),
                'floor' => (int)$dataItem->floorNumber,

                # условия сделки
                'price' => (int)$dataItem->bargainTerms->price, // цена в месяц
                'client_fee' => (int)$dataItem->bargainTerms->clientFee,
                'agent_fee' => (int)$dataItem->bargainTerms->agentFee,
                'deposit' => (int)$dataItem->bargainTerms->deposit,

                # даты
                'updated_date' => date('r', (int)$dataItem->addedTimestamp), // "сегодня, 10:39"
                'created_date' => date('r', strtotime((string)$dataItem->creationDate)), // 2020-12-24T12:27:27.003
                'parsed_date' => date('r', time()), // когда парсили

                # другое
                'is_imported' => (int)$dataItem->isImported,
                'is_new' => (int)$dataItem->isNew,

                # пользак, что связан с обьявой
                'user' => $cianUser,

                //'photos' => $this->photoHandler() // @todo
            ]);
        }

        return $this->offersArr;
    }

    /**
     * @param array $phones
     * @return array
     */
    protected function phoneHandler(array $phones): array
    {
        return array_map(function($phone){
            return $phone->number;
        }, $phones);
    }

    /**
     * @param $data
     * @return string
     */
    protected function addressHandler($data): string
    {
        $house = $street = '';
        foreach ($data->geo->address as $address){
            if ($address->type === 'house') $house = $address->fullName;
            if ($address->type === 'street') $street = $address->fullName;
        }
        return trim("$street $house");
    }

    /**
     * @param array $photos
     * @return array
     */
    protected function photoHandler(array $photos): array
    {
        return array_map(function($photo){return [$photo->id => $photo->thumbnailUrl];}, $photos);
    }
}