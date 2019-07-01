<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2019 Intelliants, LLC <https://intelliants.com>
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

class iaPortfolio extends abstractModuleFront
{
    protected static $_table = 'portfolio';
    protected $_tablePortfolioCategories = 'portfolio_categs';

    protected $_itemName = 'portfolio';

    private $_foundRows = 0;

    public $coreSearchEnabled = false;

    public function getUrl(array $data)
    {
        $id = !empty($data['id']) ? $data['id'] : '';
        $category_alias = isset($data['category_alias']) ? $data['category_alias'] : '';
        $title_alias = isset($data['title_alias']) ? $data['title_alias'] : '';

        return $this->getInfo('url') . sprintf($this->getItemName() . '/%s/%d-%s.html', $category_alias, $id, $title_alias);
    }

    public function url($action, array $data)
    {
        return $this->getUrl($data);
    }

    public function getFoundRows()
    {
        return $this->_foundRows;
    }

    public function getById($id, $process = true)
    {
        $row = $this->_getQuery('p.`id` = ' . (int)$id, '', 1, 0, false, true);
        $row && $row = array_shift($row);

        $process && $this->_processValues($row, true);

        return $row;
    }

    public function getAll($where, $fields = null, $start = null, $limit = null)
    {
        $direction = iaDb::ORDER_DESC;
        $this->iaCore->get('portfolio_entries_direction') == 'Ascending' && $direction = iaDb::ORDER_ASC;

        switch ($this->iaCore->get('portfolio_entries_order')) {
            case 'Alphabetic':
                $orderField = 'title';
                break;

            case 'Order':
                $orderField = 'order';
                break;

            case 'Date':
            default:
                $orderField = 'date_added';
                break;
        }

        !empty($where) ? $where : '';

        $where = !empty($where) ? $where . ' AND ' : '';
        $where.= 'p.`status` = "active"';

        $order = "p.`{$orderField}` {$direction}";
        $rows = $this->_getQuery($where, $order, $limit, $start, true);

        return $rows;
    }

    protected function _getQuery($aWhere = '', $aOrder = '', $limit = null, $start = null, $foundRows = false, $singleRow = false)
    {
        $sql = <<<SQL
SELECT :found_rows :fields
FROM `:prefix:table_portfolio` p
LEFT JOIN `:prefix:table_members` m ON (p.`member_id` = m.`id`)
LEFT JOIN `:prefix:table_categories` cat ON (p.`category_id` = cat.`id`)
WHERE :where :order
SQL;

        if ($start !== null && $limit !== null) {
            $sql .= ' LIMIT :start, :limit';
        }

        $where = [
            "(m.`status` = 'active' OR m.`status` IS NULL)",
        ];

        $aWhere && $where[] = $aWhere;
        $aOrder && $aOrder = str_replace('`title`', '`title_' . $this->iaView->language . '`', $aOrder);

        $data = [
            'found_rows' => ($foundRows === true ? 'SQL_CALC_FOUND_ROWS' : ''),
            'prefix' => $this->iaDb->prefix,
            'fields' => 'p.*'
                . ', m.`fullname` `fullname`'
                . ', cat.`title_alias` `category_alias`, cat.`title_:lang` `category_title`, cat.`num_listings` `category_num_listings`',
            'table_portfolio' => self::getTable(),
            'table_members' => iaUsers::getTable(),
            'table_categories' => $this->_tablePortfolioCategories,
            'lang' => $this->iaCore->language['iso'],
            'where' => implode(' AND ', $where),
            'order' => ($aOrder ? ' ORDER BY ' . $aOrder : ''),
            'start' => $start,
            'limit' => $limit
        ];

        $rows = $this->iaDb->getAll(iaDb::printf($sql, $data));

        if ($foundRows === true) {
            $this->_foundRows = $this->iaDb->foundRows();
        } elseif ($foundRows == 'count') {
            $data['fields'] = 'COUNT(*) `count`';
            $data['order'] = '';
            $data['start'] = 0;
            $data['limit'] = 1;

            $this->_foundRows = $this->iaDb->getOne(iaDb::printf($sql, $data));
        }

        $this->_processValues($rows, $singleRow);

        return $rows;
    }
}