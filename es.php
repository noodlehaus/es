<?php

# initialize an ES conn (lambda)
function es_init($host, $port, $index) {

  $es = "http://{$host}:{$port}/{$index}/";
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, false);

  return function ($verb, $path, array $data) use ($es, $ch) {

    curl_setopt($ch, CURLOPT_URL, "{$es}/{$path}");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);

    if (!empty($data)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $rs = curl_exec($ch);
    if ($rs !== false) {
      return json_decode($rs, true);
    }

    trigger_error('es: '.curl_error($ch), E_USER_ERROR);
  };
}

# inserts a document into the ES type collection
function es_insert($conn, $type, $id, $doc) {
  return es_call('PUT', "{$type}/{$id}", $doc);
}

# invokes search endpoint using ES conn (lambda)
function es_search($conn, $type, $query) {
  return $conn('POST', "{$type}/_search", $query);
}

# performs partial document update
function es_partial_update($conn, $type, $id, $doc) {
  return $conn('POST', "{$type}/{$id}/_update", ['doc' => $doc]);
}

# removes document from the index
function es_delete($conn, $type, $id) {
  return $conn('DELETE', "{$type}/{$id}");
}
