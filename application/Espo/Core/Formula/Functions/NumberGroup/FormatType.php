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

namespace Nadlani\Core\Formula\Functions\NumberGroup;

use \Nadlani\Core\Exceptions\Error;

class FormatType extends \Nadlani\Core\Formula\Functions\Base
{
    protected function init()
    {
        $this->addDependency('number');
    }

    public function process(\StdClass $item)
    {
        if (!property_exists($item, 'value')) {
            return true;
        }

        if (!is_array($item->value)) {
            throw new Error();
        }

        if (count($item->value) < 1) {
             throw new Error();
        }

        $decimals = null;
        if (count($item->value) > 1) {
            $decimals = $this->evaluate($item->value[1]);
        }

        $decimalMark = null;
        if (count($item->value) > 2) {
            $decimalMark = $this->evaluate($item->value[2]);
        }

        $thousandSeparator = null;
        if (count($item->value) > 3) {
            $thousandSeparator = $this->evaluate($item->value[3]);
        }

        $value = $this->evaluate($item->value[0]);

        return $this->getInjection('number')->format($value, $decimals, $decimalMark, $thousandSeparator);
    }
}