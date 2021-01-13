<?php

namespace Cli\Commands;

use Services\Cian\CianDataHandler;
use Services\Cian\CianDataProvider;

class CianParseCommand extends Command
{
    protected $dataProvider;
    protected $dataHandler;

    /**
     * @var int|null $forceUpdate - если указана 1, то тянем данные из Cian повторно
     */
    protected $forceUpdate;

    public function __construct(array $params)
    {
        parent::__construct($params);

        // @todo param storeSource = csv|db

        $this->dataProvider = new CianDataProvider();
        $this->dataHandler = new CianDataHandler();

        $this->forceUpdate = $this->getParam('forceUpdate');
    }

    public function execute()
    {
        try {
            $data = $this->dataProvider->queryData($this->forceUpdate);
        } catch (\Exception $exception){
            echo $exception;
            return false;
        }

        $offersArr = $this->dataHandler->process($data);

        foreach ($offersArr as $offer){
            $offer->save();
            echo "\rOffer сохранен, id:". $offer->id;
        }

        echo "\nГотово!". PHP_EOL;
    }
}