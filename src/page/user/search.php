<?php
JsonPage::start();

$namePattern = str::getOrPost('namePattern', '%');
$offset = (int)str::getOrPost('offset', '0');
$limit = (int)str::getOrPost('limit', '50');

$data = userinfo::search($namePattern, $offset, $limit);

JsonPage::output($data);
