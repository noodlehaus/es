<?php declare(strict_types=1);

# initialize an ES conn (lambda)
function es_init(string $host, int $port, string $index) {

  $es = "http://{$host}:{$port}/{$index}";
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, false);

  return function (string $verb, string $path, array $data = []) use ($es, $ch) {

    curl_setopt($ch, CURLOPT_URL, "{$es}/{$path}");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);

    if (!empty($data)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $rs = curl_exec($ch);
    if ($rs !== false) {
      return json_decode($rs, true);
    }

    trigger_error('es: '.curl_error($ch), E_USER_ERROR);
  };
}

# inserts a document into the ES type collection
function es_insert(callable $conn, string $type, string $id, array $doc) {
  return $conn('PUT', "{$type}/{$id}", $doc);
}

# invokes search endpoint using ES conn (lambda)
function es_search(callable $conn, string $type, array $query) {
  return $conn('POST', "{$type}/_search", $query);
}

# performs partial document update
function es_partial_update(callable $conn, string $type, string $id, array $doc) {
  return $conn('POST', "{$type}/{$id}/_update", ['doc' => $doc]);
}

# removes document from the index
function es_delete(callable $conn, string $type, string $id) {
  return $conn('DELETE', "{$type}/{$id}");
}
