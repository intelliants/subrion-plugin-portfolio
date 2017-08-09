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

class iaCategories extends iaAbstractHelperCategoryFlat
{
    protected static $_table = 'portfolio_categs';
    protected $_itemName = 'portfolio_categs';

    protected $_activityLog = ['item' => 'portfolio_categs'];

    protected $_recountOptions = [
        'listingsTable' => 'portfolio'
    ];


    public function insert(array $entryData)
    {
        $entryData['order'] = (int)$this->iaDb->getMaxOrder(self::getTable()) + 1;

        return parent::insert($entryData);
    }

    // placed due to bug in category helper in the core v4.1.4
    // TODO: get rid of in newer version of the package
    public function delete($itemId)
    {
        $ids = $this->iaDb->onefield('child_id', iaDb::convertIds($itemId, 'parent_id'),
            null, null, $this->getTableFlat());

        $result = parent::delete($itemId);

        if ($result && $ids) {
            $ids = implode(',', $ids);
            $this->iaDb->delete('`id` IN (' . $ids . ')', self::getTable());
        }

        return $result;
    }

    public function existsAlias($alias, $excludedCategoryId = 0)
    {
        $stmt = '`title_alias` = :alias';
        if ($excludedCategoryId) {
            $stmt .= ' && `id` != :category';
        }

        return $this->iaDb->exists($stmt, ['alias' => $alias, 'category' => $excludedCategoryId], self::getTable());
    }

    public function validateAliases($entryId, $alias, $newAlias)
    {
        if ($alias == $newAlias) {
            return;
        }

        $stmtWhere = iaDb::printf('`id` != :id && `id` IN (SELECT DISTINCT `child_id` FROM `:table_flat` WHERE `parent_id` = :id)',
            [
                'table_flat' => $this->getTableFlat(true),
                'id' => (int)$entryId
            ]);

        $stmtUpdate = 'REPLACE(`title_alias`, :alias, :new_alias)';
        $this->iaDb->bind($stmtUpdate, [
            'alias' => $alias,
            'new_alias' => $newAlias
        ]);

        $this->iaDb->update(null, $stmtWhere, ['title_alias' => $stmtUpdate], self::getTable());
    }
}
