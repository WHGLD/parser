<?php

namespace Services\Cian;

use Services\DataProviderInterface;

class CianDataProvider implements DataProviderInterface
{
    /** @var string у каждой улицы свой id в базе cian */
    CONST CIAN_STREET_ID = '259';
    /** @var string количество комнат, если хотим еще искать двушки, то правим на 1,2 */
    CONST ROOMS_COUNT = '1';

    protected $cacheKey;

    public function __construct()
    {
        $this->cacheKey = '../../cache/cache-of-'.date('Y-m-d').'.txt';
    }

    /**
     * Парсим 1 раз в сутки, если не задан $forceUpdate
     * @param $forceUpdate - игнорируем условие парсинга 1 раз в сутки
     * @return array
     * @throws \Exception
     */
    public function queryData(?int $forceUpdate): array
    {
        $cache = file_exists($this->cacheKey) ? file_get_contents($this->cacheKey) : null;

        if ($cache && is_null($forceUpdate)){
            throw new \Exception('Cian data is parsed today');
        } else {
            if ($cache){ // если есть кэш, то проверим есть ли какие обновления в данных

                return json_decode($cache); // теперь запускам чз форсе с данными из кэша

                $freshData = $this->curlRequest();
                if (json_decode($cache) === $freshData) {
                    throw new \Exception('Nothing to update');
                } else {
                    return $freshData;
                }
            } else {
                return $this->curlRequest();
            }
        }
    }

    /**
     * @throws \Exception
     * @return array
     */
    protected function curlRequest(): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.cian.ru/search-offers/v2/search-offers-desktop/");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            '{"jsonQuery":{"for_day":{"type":"term","value":"!1"},"_type":"flatrent","geo":{"type":"geo","value":[{"type":"street","id":'.self::CIAN_STREET_ID.'}]},"engine_version":{"type":"term","value":2},"room":{"type":"terms","value":['.self::ROOMS_COUNT.']}}}'
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($response);
        if ($response->status !== 'ok' || !isset($response->data->offersSerialized)){
            throw new \Exception('Cian data request error');
        } else {
            file_put_contents($this->cacheKey, json_encode($response->data->offersSerialized));
            return $response->data->offersSerialized;
        }
    }
}