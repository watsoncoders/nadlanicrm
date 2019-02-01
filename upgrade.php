<?php
/************************************************************************
 * This file is part of NadlaniCrm.
 *
 * NadlaniCrm - Open Source CRM application.
 * Copyright (C) 2014-2018 Pablo Rotem
 * Website: https://www.facebook.com/sites4u2
 *
 * NadlaniCrm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NadlaniCrm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NadlaniCrm. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "NadlaniCrm" word.
 ************************************************************************/

$sapiName = php_sapi_name();

if (substr($sapiName, 0, 3) != 'cli') {
    die("Upgrade script can be run only via CLI.\n");
}

include "bootstrap.php";

$arg = isset($_SERVER['argv'][1]) ? trim($_SERVER['argv'][1]) : '';

if ($arg == 'version' || $arg == '-v') {
    $app = new \Nadlani\Core\Application();
    die("Current version is " . $app->getContainer()->get('config')->get('version') . ".\n");
}

if (empty($arg)) {
    die("Specify an upgrade package file.\n");
}

if (!file_exists($arg)) {
    die("Package file does not exist.\n");
}

$pathInfo = pathinfo($arg);
if (!isset($pathInfo['extension']) || $pathInfo['extension'] !== 'zip' || !is_file($arg)) {
    die("Unsupported package.\n");
}

$app = new \Nadlani\Core\Application();

$config = $app->getContainer()->get('config');
$entityManager = $app->getContainer()->get('entityManager');

$user = $entityManager->getEntity('User', 'system');
$app->getContainer()->setUser($user);

$upgradeManager = new \Nadlani\Core\UpgradeManager($app->getContainer());

echo "Current version is " . $config->get('version') . "\n";
echo "Start upgrade process...\n";

try {
    $fileData = file_get_contents($arg);
    $fileData = 'data:application/zip;base64,' . base64_encode($fileData);

    $upgradeId = $upgradeManager->upload($fileData);
    $upgradeManager->install(array('id' => $upgradeId));
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

try {
    $app = new \Nadlani\Core\Application();
    $app->runRebuild();
} catch (\Exception $e) {}

echo "Upgrade is complete. New version is " . $config->get('version') . ". \n";
