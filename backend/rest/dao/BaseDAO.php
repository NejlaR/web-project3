<?php

require_once __DIR__ . '/../../Database.php';
class BaseDAO
{
    protected $connection;
    protected $table_name;
    protected $primary_key;
    protected $allowed_columns;

    public function __construct(
        $table_name,
        $primary_key,
        $allowed_columns
    ) {
        $this->connection = Database::connect();
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        $this->allowed_columns = $allowed_columns;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM `{$this->table_name}`";

        return $this->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                WHERE `{$this->primary_key}` = :id";

        return $this->query_unique($sql, ['id' => $id]);
    }

    public function add($entity)
    {
        $this->validateColumns($entity);

        $columns = '`'
            . implode('`, `', array_keys($entity))
            . '`';

        $placeholders = ':'
            . implode(', :', array_keys($entity));

        $sql = "INSERT INTO `{$this->table_name}` ($columns)
                VALUES ($placeholders)";

        $statement = $this->connection->prepare($sql);
        $statement->execute($entity);

        return $this->connection->lastInsertId();
    }

    public function update($entity, $id)
    {
        $this->validateColumns($entity);

        $assignments = [];

        foreach ($entity as $column => $value) {
            $assignments[] = "`$column` = :$column";
        }

        $sql = "UPDATE `{$this->table_name}` SET "
            . implode(', ', $assignments)
            . " WHERE `{$this->primary_key}` = :_id";

        $entity['_id'] = $id;

        $statement = $this->connection->prepare($sql);

        return $statement->execute($entity);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM `{$this->table_name}`
                WHERE `{$this->primary_key}` = :id";

        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    protected function query($sql, $params = [])
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function query_unique($sql, $params = [])
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    protected function validateColumns($entity)
    {
        if (!is_array($entity) || empty($entity)) {
            throw new InvalidArgumentException(
                'Potrebno je poslati podatke za unos ili izmjenu.'
            );
        }

        foreach (array_keys($entity) as $column) {
            if (!in_array($column, $this->allowed_columns, true)) {
                throw new InvalidArgumentException(
                    'Nedozvoljeno polje: ' . $column
                );
            }
        }
    }
}