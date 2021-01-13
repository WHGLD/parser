<?php

namespace Models;

use Services\Db;

abstract class Model
{
    abstract protected static function getTableName(): string;

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data): self
    {
        $offer = new static();
        foreach ($data as $fieldName => $value){
            $offer->$fieldName = $value;
        }
        return $offer;
    }

    public function __set($name, $value)
    {
        if (property_exists(self::class, $this->$name)){
            $this->$name = $value;
        }
    }

    /** @var int */
    protected $id;

    /**
     * @param int $id
     * @return static|null
     */
    public static function getById(int $id): ?self
    {
        $entities = Db::getInstance()->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id=:id;',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    /**
     * @return array
     */
    public static function findAll(): array
    {
        return Db::getInstance()->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);
    }

    /**
     * @param $field
     * @param $value
     * @return array
     */
    public static function getByField($field, $value): array
    {
        return Db::getInstance()->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE '.$field.'=:field_value',
            [':field_value' => $value],
            static::class
        );
    }

    /**
     * @return static
     */
    public function save(): self
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $mappedProperties[$propertyName] = $this->$propertyName;
        }

        return $mappedProperties;
    }

    /**
     * @param array $mappedProperties
     * @return void
     */
    protected function insert(array $mappedProperties): void
    {
        $filteredProperties = array_filter($mappedProperties);

        $columns = [];
        $paramsNames = [];
        $params2values = [];
        foreach ($filteredProperties as $columnName => $value) {
            $columns[] = '`' . $columnName. '`';
            $paramName = ':' . $columnName;
            $paramsNames[] = $paramName;
            $params2values[$paramName] = $value;
        }

        $columnsViaSemicolon = implode(', ', $columns);
        $paramsNamesViaSemicolon = implode(', ', $paramsNames);

        $sql = 'INSERT INTO ' . static::getTableName() . ' (' . $columnsViaSemicolon . ') VALUES (' . $paramsNamesViaSemicolon . ');';

        Db::getInstance()->query($sql, $params2values, static::class);

        $this->id = Db::getInstance()->getLastInsertId();
    }

    /**
     * @param array $mappedProperties
     * @return void
     */
    protected function update(array $mappedProperties): void
    {
        $columns2params = [];
        $params2values = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index; // :param1
            $columns2params[] = $column . ' = ' . $param; // column1 = :param1
            $params2values[':param' . $index] = $value; // [:param1 => value1]
            $index++;
        }
        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) . ' WHERE id = ' . $this->id;

        Db::getInstance()->query($sql, $params2values, static::class);
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        Db::getInstance()->query(
            'DELETE FROM `' . static::getTableName() . '` WHERE id = :id',
            [':id' => $this->id]
        );
        $this->id = null;
    }
}