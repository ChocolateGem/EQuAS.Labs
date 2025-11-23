<?php

// Інтерфейс спільного будівельника
interface QueryBuilder {
    public function select($table, $fields = ['*']): QueryBuilder;
    public function where($field, $operator, $value): QueryBuilder;
    public function limit($limit): QueryBuilder;
    public function getSQL(): string;
}

class MySQLQueryBuilder implements QueryBuilder {
    protected $query = [
        'select' => '',
        'where' => [],
        'limit' => ''
    ];

    public function select($table, $fields = ['*']): QueryBuilder {
        $columns = implode(', ', $fields);
        $this->query['select'] = "SELECT {$columns} FROM `{$table}`";
        return $this;
    }

    public function where($field, $operator, $value): QueryBuilder {
        $value = is_string($value) ? "'{$value}'" : $value;
        $this->query['where'][] = "`{$field}` {$operator} {$value}";
        return $this;
    }

    public function limit($limit): QueryBuilder {
        $this->query['limit'] = "LIMIT {$limit}";
        return $this;
    }

    public function getSQL(): string {
        $sql = $this->query['select'];
        if (!empty($this->query['where'])) {
            $sql .= " WHERE " . implode(' AND ', $this->query['where']);
        }
        if ($this->query['limit']) {
            $sql .= " " . $this->query['limit'];
        }
        return $sql . ";";
    }
}

class PostgreSQLQueryBuilder implements QueryBuilder {
    protected $query = [
        'select' => '',
        'where' => [],
        'limit' => ''
    ];

    public function select($table, $fields = ['*']): QueryBuilder {
        $columns = implode(', ', $fields);
        $this->query['select'] = "SELECT {$columns} FROM \"{$table}\"";
        return $this;
    }

    public function where($field, $operator, $value): QueryBuilder {
        $value = is_string($value) ? "'{$value}'" : $value;
        $this->query['where'][] = "\"{$field}\" {$operator} {$value}";
        return $this;
    }

    public function limit($limit): QueryBuilder {
        $this->query['limit'] = "LIMIT {$limit}";
        return $this;
    }

    public function getSQL(): string {
        $sql = $this->query['select'];
        if (!empty($this->query['where'])) {
            $sql .= " WHERE " . implode(' AND ', $this->query['where']);
        }
        if ($this->query['limit']) {
            $sql .= " " . $this->query['limit'];
        }
        return $sql . ";";
    }
}

function clientCode(QueryBuilder $builder) {
    echo "Запит для " . get_class($builder) . "\n";
    $sql = $builder
        ->select("users", ["id", "name", "email"])
        ->where("active", "=", 1)
        ->where("age", ">", 18)
        ->limit(10)
        ->getSQL();

    echo $sql . "\n\n";
}

$mysqlBuilder = new MySQLQueryBuilder();
clientCode($mysqlBuilder);

$pgsqlBuilder = new PostgreSQLQueryBuilder();
clientCode($pgsqlBuilder);
?>
