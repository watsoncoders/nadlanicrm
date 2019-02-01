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

namespace Nadlani\Controllers;

use \Nadlani\Core\Exceptions\NotFound;
use \Nadlani\Core\Exceptions\Error;
use \Nadlani\Core\Exceptions\Forbidden;
use \Nadlani\Core\Exceptions\BadRequest;

class Admin extends \Nadlani\Core\Controllers\Base
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function postActionRebuild($params, $data, $request)
    {
        if (!$request->isPost()) {
            throw new BadRequest();
        }
        $result = $this->getContainer()->get('dataManager')->rebuild();

        return $result;
    }

    public function postActionClearCache($params)
    {
        $result = $this->getContainer()->get('dataManager')->clearCache();
        return $result;
    }

    public function actionJobs()
    {
        $scheduledJob = $this->getContainer()->get('scheduledJob');

        return $scheduledJob->getAvailableList();
    }

    public function postActionUploadUpgradePackage($params, $data)
    {
        if ($this->getConfig()->get('restrictedMode')) {
            if (!$this->getUser()->isSuperAdmin()) {
                throw new Forbidden();
            }
        }
        $upgradeManager = new \Nadlani\Core\UpgradeManager($this->getContainer());

        $upgradeId = $upgradeManager->upload($data);
        $manifest = $upgradeManager->getManifest();

        return array(
            'id' => $upgradeId,
            'version' => $manifest['version'],
        );
    }

    public function postActionRunUpgrade($params, $data)
    {
        if ($this->getConfig()->get('restrictedMode')) {
            if (!$this->getUser()->isSuperAdmin()) {
                throw new Forbidden();
            }
        }

        $upgradeManager = new \Nadlani\Core\UpgradeManager($this->getContainer());
        $upgradeManager->install(get_object_vars($data));

        return true;
    }

    public function actionCronMessage($params)
    {
        return $this->getContainer()->get('scheduledJob')->getSetupMessage();
    }

    public function actionAdminNotificationList($params)
    {
        $adminNotificationManager = new \Nadlani\Core\Utils\AdminNotificationManager($this->getContainer());
        return $adminNotificationManager->getNotificationList();
    }

    public function actionSystemRequirementList($params)
    {
        $systemRequirementManager = new \Nadlani\Core\Utils\SystemRequirements($this->getContainer());
        return $systemRequirementManager->getAllRequiredList();
    }
}
