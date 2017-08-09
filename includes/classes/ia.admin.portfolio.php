<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

class iaPortfolio extends abstractModuleAdmin
{
    protected static $_table = 'portfolio';
    protected $_itemName = 'portfolio';

    protected $_activityLog = ['item' => 'portfolio'];


    public function titleAlias($entryTitle, $categoryAlias = null)
    {
        $alias = iaSanitize::alias($entryTitle);

        if (is_null($categoryAlias)) {
            return $alias;
        }

        $id = (isset($_GET['id']) && (int)$_GET['id'] > 0) ? (int)$_GET['id'] : $this->iaDb->getNextId(self::getTable(true));
        $alias = $id . '-' . $alias;

        empty($categoryAlias) || $alias = $categoryAlias . IA_URL_DELIMITER . $alias;

        $baseUrl = $this->getInfo('url');

        return $baseUrl . $alias . '.html';
    }
}
