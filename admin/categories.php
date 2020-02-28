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

class iaBackendController extends iaAbstractControllerModuleBackend
{
    protected $_name = 'categories';

    protected $_itemName = 'portfolio_categ';

    protected $_helperName = 'categories';

    protected $_gridColumns = ['title', 'title_alias', 'num_listings', 'num_all_listings', 'order', 'status'];
    protected $_gridFilters = ['title' => self::LIKE, 'status' => self::EQUAL];

    protected $_activityLog = ['item' => 'category'];


    protected function _setDefaultValues(array &$entry)
    {
        $entry = [
            'title_alias' => '',
            'locked' => false,
            'level' => 1,
            'status' => iaCore::STATUS_ACTIVE,
            iaCategories::COL_PARENT_ID => $this->getHelper()->getRootId()
        ];
    }

    protected function _preSaveEntry(array &$entry, array $data, $action)
    {
        parent::_preSaveEntry($entry, $data, $action);

        $entry[iaCategories::COL_PARENT_ID] = isset($data['tree_id']) ? (int)$data['tree_id'] : iaCategories::ROOT_PARENT_ID;
        $entry['status'] = $data['status'];
        $entry['locked'] = (int)$data['locked'];
        $entry['title_alias'] = '';

        if (isset($data['title_alias'])) {
            $entry['title_alias'] = empty($data['title_alias']) ? $data['title'][$this->_iaCore->language['iso']] : $data['title_alias'];
            $entry['title_alias'] = iaSanitize::alias($entry['title_alias']);

            if ($entry[iaCategories::COL_PARENT_ID] && $entry[iaCategories::COL_PARENT_ID] != $this->getHelper()->getRootId()) {
                $parentCategory = $this->getHelper()->getById($entry[iaCategories::COL_PARENT_ID]);
                $entry['title_alias'] = $parentCategory['title_alias'] . IA_URL_DELIMITER . $entry['title_alias'];
            }

            if ($entry['title_alias'] && $this->getHelper()->existsAlias($entry['title_alias'], $this->getEntryId())) {
                $this->addMessage('category_already_exists');
            }
        }

        return !$this->getMessages();
    }

    protected function _postSaveEntry(array &$entry, array $data, $action)
    {
        // reset cache to avoid confusion
        $this->_iaCore->iaCache->remove('popular_ycategs');
    }

    protected function _assignValues(&$iaView, array &$entryData)
    {
        parent::_assignValues($iaView, $entryData);

        $titleAlias = explode(IA_URL_DELIMITER, $entryData['title_alias']);
        $entryData['title_alias'] = end($titleAlias);

        $iaView->assign('tree', $this->getHelper()->getTreeVars($this->getEntryId(), $entryData, $this->getPath()));
    }

    protected function _getJsonAlias(array $params)
    {
        $output = ['data' => $this->getHelper()->getInfo('url')];

        if (isset($params['id']) && $params['id'] == $this->getHelper()->getRootId()) {
            return $output;
        }

        $title = isset($params['title']) ? $params['title'] : '';
        $title = iaSanitize::alias($title);

        $category = isset($params['category']) ? (int)$params['category'] : 0;
        $alias = null;
        if ($category > 0) {
            $alias = $this->_iaDb->one('title_alias', iaDb::convertIds($category));
        }

        $data = [
            'id' => (isset($params['id']) && (int)$params['id'] > 0) ? (int)$params['id'] : '{id}',
            'title_alias' => ($alias ? $alias . IA_URL_DELIMITER : '') . $title,
        ];

        if ($this->getHelper()->existsAlias($data['title_alias'], $data['id'])) {
            $output['exists'] = iaLanguage::get('category_already_exists');
        }

        $output['data'] .= $data['title_alias'];

        return $output;
    }

    protected function _getJsonConsistency(array $data)
    {
        $output = [];

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'recount_entries':
                    $this->getHelper()->recount($_POST['start'], $_POST['limit']);
                    break;

                case 'count':
                    $this->getHelper()->resetCounters();

                    $this->_iaCore->factoryModule('listing', $this->getModuleName(), iaCore::ADMIN);
                    $output['total'] = $this->_iaDb->one(iaDb::STMT_COUNT_ROWS,
                        iaDb::convertIds(iaCore::STATUS_ACTIVE, 'status'), 'portfolio');
            }
        }

        return $output;
    }

    protected function _insert(array $entryData)
    {
        return $this->getHelper()->insert($entryData);
    }

    protected function _update(array $entryData, $entryId)
    {
        return $this->getHelper()->update($entryData, $entryId);
    }

    protected function _delete($entryId)
    {
        return $this->getHelper()->delete($entryId);
    }
}
