<?php

require './es.php';

$conn = es_init('localhost', 9200, 'grocer');

es_insert($conn, 'fruits', 2, ['name' => 'apple', 'color' => 'red']);
es_insert($conn, 'fruits', 3, ['name' => 'strawberry', 'color' => 'red']);

# give it some time to update
sleep(1);

var_dump(es_search($conn, 'fruits', ['query' => ['match' => ['color' => 'red']]]));

es_delete($conn, 'fruits', 2);

# give it time to update before checking for changes
sleep(1);

var_dump(es_search($conn, 'fruits', ['query' => ['match' => ['color' => 'red']]]));
