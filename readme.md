## Parser
##### Выгружаем данные по аренде квартир с api.cian, по параметрам: кол-во комнта и cian street id.
##### @todo

- добавить возможность задавать cian_street_id или если поиска endpoint, то полноценный адрес
- сохранение номеров телефонов
- сохранение картинок
- простое графическое отображение
- добавить в качестве источника яндекс недвижемость (надо будет подправить таблицы + добавить команду+provider+handler)

##### Запуск чз command line interface
##### Установлено ограничение на парсинг - 1 раз в сутки

php cli.php CianParse

параметры: -forceUpdate=1 игнорируем ограничение парсинга 1 раз в сутки,
будет выполнена повторный парсинг. Пока в этом параметре мало толку, надо доделать обработку обновлений.


