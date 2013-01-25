<?php

function __dependency($group, $priority, $after, $list = null) {
  // Trim and Make-sure that $group is not an empty string
  $group = trim($group);
  if (strlen($group) == 0) {
    $group = null;
  }

  assert('isset($group) && is_string($group)');
  assert('isset($priority) && is_integer($priority)');
  assert('!isset($list) || is_array($list)');
  assert('isset($after) && is_bool($after)');

  $e = array('group' => $group, 'priority' => $priority, 'after' => $after);
  if (!isset($list)) {
    return array($e);
  } else {
    // Add Element to End of Array
    $list[] = $e;
    return $list;
  }
}

function after($group, $priority, $list = null) {
  return __dependency($group, $priority, true, $list);
}

function before($group, $priority, $list = null) {
  return __dependency($group, $priority, false, $list);
}

function string_onEmpty($value, $default = null) {
  if (isset($value) && is_string($value)) {
    $value = trim($value);
    return count($value) ? $value : $default;
  } else {
    return $default;
  }
}

function integer_gt($value, $min = 0, $default = null) {
  if (isset($value) && is_integer($value)) {
    return $value > $min ? $value : $default;
  } else {
    return $default;
  }
}

?>