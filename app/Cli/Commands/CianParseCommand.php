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

    /**
     * @return void
     * @throws \Exception
     */
    public function execute(): void
    {
        $data = $this->dataProvider->queryData($this->forceUpdate);

        $offersArr = $this->dataHandler->process($data);

        foreach ($offersArr as $offer){
            $offer->save();
            exit;
            echo "\rOffer сохранен, id:". $offer->id;
        }

        echo "\nГотово!". PHP_EOL; // @todo сколько сохранили записей
    }
}