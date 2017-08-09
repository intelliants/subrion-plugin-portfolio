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

$iaPortfolio = $iaCore->factoryModule('portfolio', 'portfolio');

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $page = 'index';

    array_shift($iaView->url);

    if ((int)end($iaView->url)) {
        $page = 'view';
    } elseif (!empty($iaView->url)) {
        $page = 'category';
    }

    switch ($page) {
        case 'index':
            $pagination = [
                'total' => 0,
                'limit' => (int)$iaCore->get('portfolio_entries_per_page'),
                'url' => $iaCore->factory('page', iaCore::FRONT)->getUrlByName('portfolio') . '?page={page}'
            ];

            $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
            $start = ($page - 1) * $pagination['limit'];

            $rows = $iaPortfolio->getAll(iaDb::EMPTY_CONDITION, iaDb::ALL_COLUMNS_SELECTION, $start, $pagination['limit']);

            $pagination['total'] = $iaPortfolio->getFoundRows();

            $iaView->assign('entries', $rows);
            $iaView->assign('pagination', $pagination);

            if ($iaCore->get('portfolio_disable_columns')) {
                unset($iaCore->iaView->blocks['left'], $iaCore->iaView->blocks['right']);
            }

            $iaView->display('index');
            break;

        case 'category':
            $iaCategory = $iaCore->factoryModule('categories', 'portfolio');
            $title_alias = implode('/', $iaView->url);

            $category = $iaCategory->getOne("`title_alias` = '{$title_alias}'");

            if (!$category) {
                return iaView::errorPage(iaView::ERROR_NOT_FOUND);
            }

            $pagination = [
                'total' => 0,
                'limit' => (int)$iaCore->get('portfolio_entries_per_page'),
                'url' => $iaCore->factory('page', iaCore::FRONT)->getUrlByName('portfolio') . $title_alias . '/?page={page}'
            ];

            $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
            $start = ($page - 1) * $pagination['limit'];

            $categoryParents = $iaCategory->getParents($category['id']);
            $categoryChildren = $iaCategory->getChildren($category['id']);
            $categoryIds = [$category['id']];

            if ($iaCore->get('portfolio_show_children_entries') && !empty($categoryChildren)) {
                foreach ($categoryChildren as $row) {
                    $categoryIds[] = $row['id'];
                }
            }

            foreach ($categoryParents as $row) {
                if ($row['id'] !== $category['id']) {
                    iaBreadcrumb::toEnd($row['title'], IA_URL . 'portfolio/' . $row['title_alias']);
                }
            }

            $where = 'p.`category_id` IN (' . implode(',', $categoryIds) . ')';
            $rows = $iaPortfolio->getAll($where, iaDb::ALL_COLUMNS_SELECTION, $start, $pagination['limit']);

            $pagination['total'] = $iaPortfolio->getFoundRows();

            iaBreadcrumb::toEnd($category['title'], IA_SELF);

            $iaView->title(iaSanitize::tags($category['title']));

            $iaView->assign('entries', $rows);
            $iaView->assign('categories', $categoryChildren);
            $iaView->assign('pagination', $pagination);

            if ($iaCore->get('portfolio_disable_columns')) {
                unset($iaCore->iaView->blocks['left'], $iaCore->iaView->blocks['right']);
            }

            $iaView->display('category');
            break;

        default:
            $id = (int)end($iaCore->requestPath);

            $entry = $iaPortfolio->getById($id);

            if (!$entry) {
                return iaView::errorPage(iaView::ERROR_NOT_FOUND);
            }

            $iaCategory = $iaCore->factoryModule('categories', 'portfolio');

            $openGraph = [
                'title' => $entry['title'],
                'description' => iaSanitize::tags($entry['body'])
            ];

            $entry['gallery'] && $openGraph['image'] = IA_CLEAR_URL . 'uploads/' . $entry['gallery'][0]['path'] . 'large/' . $entry['gallery'][0]['file'];

            $categoryParents = $iaCategory->getParents($entry['category_id']);

            foreach ($categoryParents as $row) {
                iaBreadcrumb::toEnd($row['title'], IA_URL . 'portfolio/' . $row['title_alias']);
            }

            iaBreadcrumb::toEnd($entry['title'], IA_SELF);

            $iaView->set('og', $openGraph);
            $iaView->title(iaSanitize::tags($entry['title']));

            $iaView->assign('entry', $entry);
            $iaView->assign('category', $iaCategory->getById($entry['category_id']));

            $iaView->display('view');
    }
}