<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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

class iaBackendController extends iaAbstractControllerModuleBackend
{
    protected $_name = 'portfolio';

    protected $_itemName = 'portfolio';

    protected $_helperName = 'portfolio';

    protected $_gridFilters = ['status' => self::EQUAL];
    protected $_gridSorting = [
        'category' => ['title', 'cat'],
        'member' => ['fullname', 'm'],
    ];

    protected $_tooltipsEnabled = true;

    protected $_activityLog = ['item' => 'portfolio'];

    protected $_iaCategories;


    public function init()
    {
        $this->_iaCategories = $this->_iaCore->factoryModule('categories', $this->getName(), iaCore::ADMIN);
        $this->_path = IA_ADMIN_URL . $this->getName() . IA_URL_DELIMITER;
    }

    protected function _indexPage(&$iaView)
    {
        parent::_indexPage($iaView);

        $iaView->add_css('_IA_URL_modules/fancybox/js/jquery.fancybox');
        $iaView->add_js('_IA_URL_modules/fancybox/js/jquery.fancybox.pack');
    }

    protected function _gridQuery($columns, $where, $order, $start, $limit)
    {
        $sql = <<<SQL
SELECT SQL_CALC_FOUND_ROWS p.`id`, p.`title_:lang` `title`, p.`gallery`, p.`title_alias`, p.`date_added`, p.`order`, p.`status`,
  cat.`title_:lang` `category`, m.`fullname` `member`, 1 `update`, 1 `delete`
  FROM `:prefix:table_portfolio` p
LEFT JOIN `:prefix:table_categories` cat ON (cat.`id` = p.`category_id`)
LEFT JOIN `:prefix:table_members` m ON (m.`id` = p.`member_id`)
:where
LIMIT :start, :limit
SQL;
        $sql = iaDb::printf($sql, [
            'prefix' => $this->_iaDb->prefix,
            'table_portfolio' => $this->getTable(),
            'table_categories' => iaCategories::getTable(),
            'table_members' => iaUsers::getTable(),
            'lang' => $this->_iaCore->language['iso'],
            'where' => ($where ? 'WHERE ' . $where . ' ' : '') . $order . ' ',
            'start' => $start,
            'limit' => $limit
        ]);

        return $this->_iaDb->getAll($sql);
    }

    protected function _modifyGridResult(array &$entries)
    {
        foreach ($entries as &$entry) {
            $entry['type'] = iaField::getLanguageValue($this->getItemName(), 'type', $entry['type']);

            $images = [];

            if ($gallery = unserialize($entry['gallery'])) {
                foreach ($gallery as $image) {
                    $images[] = [
                        'href' => $this->_iaCore->iaView->assetsUrl . 'uploads/' . $image['path'] . 'large/' .  $image['file'],
                        'title' => $image['title']
                    ];
                }
            }

            $entry['gallery'] = $images;
        }
    }

    protected function _setDefaultValues(array &$entry)
    {
        $entry = [
            'status' => iaCore::STATUS_ACTIVE,
            'member_id' => iaUsers::getIdentity()->id,
        ];
    }

    protected function _assignValues(&$iaView, array &$entryData)
    {
        parent::_assignValues($iaView, $entryData);

        $iaView->assign('categoryTree', $this->_getCategoryTreeVars($entryData));
    }

    private function _getCategoryTreeVars(array $entryData)
    {
        $category = empty($entryData['category_id'])
            ? $this->_iaCategories->getRoot()
            : $this->_iaCategories->getById($entryData['category_id']);

        $nodes = array_merge([$this->_iaCategories->getRootId()], $this->_iaCategories->getParents($category['id'], true));

        return [
            'url' => IA_ADMIN_URL . $this->getName() . '/categories/tree.json?noroot',
            'nodes' => implode(',', $nodes),
            'id' => $category['id'],
            'title' => isset($category['title']) ? $category['title'] : ''
        ];
    }

    protected function _getJsonSlug(array $params)
    {
        $category = $this->_iaCategories->getById((int)$_GET['category']);

        $alias = IA_URL . 'portfolio/';

        if (!empty($params['title'])) {
            $alias = $this->getHelper()->titleAlias($params['title'], $category['title_alias']);
        }

        return ['data' => $alias];
    }

    public function updateCounters($entryId, array $entryData, $action, $previousData = null)
    {
        $newStatus = $entryData['status'];
        $oldStatus = $previousData ? $previousData['status'] : $newStatus;

        if ((iaCore::ACTION_DELETE == $action && iaCore::STATUS_ACTIVE == $newStatus)
            || (iaCore::STATUS_ACTIVE == $oldStatus && iaCore::STATUS_ACTIVE != $newStatus)) {
            $diff = -1;
        } elseif ((iaCore::STATUS_ACTIVE == $newStatus && iaCore::STATUS_ACTIVE != $oldStatus) || iaCore::ACTION_ADD == $action) {
            $diff = 1;
        } elseif ($previousData['category_id'] !== $entryData['category_id']) {
            $this->_iaCategories->recountById($previousData['category_id'], -1);
            $diff = 1;
        }

        if (isset($diff)) {
            $this->_iaCategories->recountById($entryData['category_id'], $diff);
        }
    }

    protected function _preSaveEntry(array &$entry, array $data, $action)
    {
        parent::_preSaveEntry($entry, $data, $action);

        if (empty($data['tree_id'])) {
            $this->addMessage('invalid_category');
        } else {
            $entry['category_id'] = (int)$data['tree_id'];
        }

        $entry['title_alias'] = empty($data['title_alias']) ? $data['title'][$this->_iaCore->language['iso']] : $data['title_alias'];
        $entry['title_alias'] = $this->getHelper()->titleAlias($entry['title_alias']);

        return !$this->getMessages();
    }

    protected function _entryAdd(array $entryData)
    {
        $entryData['date_added'] = date(iaDb::DATETIME_FORMAT);
        $entryData['date_modified'] = date(iaDb::DATETIME_FORMAT);

        return parent::_entryAdd($entryData);
    }

    protected function _entryUpdate(array $entryData, $entryId)
    {
        $entryData['date_modified'] = date(iaDb::DATETIME_FORMAT);

        return parent::_entryUpdate($entryData, $entryId);
    }
}
